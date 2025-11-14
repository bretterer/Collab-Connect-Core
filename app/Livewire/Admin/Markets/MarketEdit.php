<?php

namespace App\Livewire\Admin\Markets;

use App\Models\Market;
use App\Models\MarketZipcode;
use App\Models\PostalCode;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;

class MarketEdit extends Component
{
    use WithFileUploads;

    public Market $market;

    public $name = '';

    public $description = '';

    public $is_active = false;

    // For CSV upload
    public $csvFile;

    // For search and add
    public $searchQuery = '';

    public $searchResults = [];

    public $selectedZipcodes = [];

    // For manual zipcode entry
    public $manualZipcode = '';

    // For map drawing
    public $drawnBoundary = null;

    public $previewZipcodes = [];

    // Active tab for tab switching
    public $activeTab = 'manual';

    // Loading state for map zipcode detection
    public $loadingZipcodes = false;

    public function mount(Market $market)
    {
        $this->market = $market;
        $this->name = $market->name;
        $this->description = $market->description;
        $this->is_active = $market->is_active;
    }

    public function updateMarket()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $this->market->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        Flux::toast('Market updated successfully!');
    }

    public function uploadCsv()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $zipcodes = [];
        $added = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $zipcode = trim($row[0]);

            // Validate that the zipcode exists in postal_codes table
            if (PostalCode::where('postal_code', $zipcode)->where('country_code', 'US')->exists()) {
                $zipcodes[] = [
                    'market_id' => $this->market->id,
                    'postal_code' => $zipcode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $added++;
            }
        }

        fclose($handle);

        // Bulk insert zipcodes
        if (! empty($zipcodes)) {
            MarketZipcode::insertOrIgnore($zipcodes);
        }

        $this->csvFile = null;

        Flux::toast("{$added} zipcodes added successfully!");
    }

    public function updatedSearchQuery()
    {
        $this->searchZipcodes();
    }

    public function searchZipcodes()
    {
        if (strlen($this->searchQuery) < 2) {
            $this->searchResults = [];

            return;
        }

        $this->searchResults = PostalCode::where('country_code', 'US')
            ->where(function ($query) {
                $query->where('postal_code', 'like', $this->searchQuery.'%')
                    ->orWhere('place_name', 'like', '%'.$this->searchQuery.'%')
                    ->orWhere('admin_name1', 'like', '%'.$this->searchQuery.'%');
            })
            ->limit(20)
            ->get();
    }

    public function toggleZipcodeSelection($postalCode)
    {
        if (in_array($postalCode, $this->selectedZipcodes)) {
            $this->selectedZipcodes = array_diff($this->selectedZipcodes, [$postalCode]);
        } else {
            $this->selectedZipcodes[] = $postalCode;
        }
    }

    public function addSelectedZipcodes()
    {
        if (empty($this->selectedZipcodes)) {
            Flux::toast('No zipcodes selected.');

            return;
        }

        $added = 0;
        foreach ($this->selectedZipcodes as $zipcode) {
            MarketZipcode::firstOrCreate([
                'market_id' => $this->market->id,
                'postal_code' => $zipcode,
            ]);
            $added++;
        }

        $this->selectedZipcodes = [];
        $this->searchResults = [];
        $this->searchQuery = '';

        Flux::toast("{$added} zipcodes added successfully!");
    }

    public function addManualZipcode()
    {
        $this->validate([
            'manualZipcode' => ['required', 'string', function ($attribute, $value, $fail) {
                if (! PostalCode::where('postal_code', $value)->where('country_code', 'US')->exists()) {
                    $fail('The postal code is not valid.');
                }
            }],
        ]);

        MarketZipcode::firstOrCreate([
            'market_id' => $this->market->id,
            'postal_code' => $this->manualZipcode,
        ]);

        $this->manualZipcode = '';

        Flux::toast('Zipcode added successfully!');
    }

    public function removeZipcode($zipcodeId)
    {
        MarketZipcode::find($zipcodeId)->delete();

        Flux::toast('Zipcode removed successfully!');
    }

    public function findZipcodesInBoundary($boundary)
    {
        // Boundary is either a circle or polygon from the map
        // Circle: { type: 'circle', center: [lng, lat], radius: miles }
        // Polygon: { type: 'polygon', coordinates: [[lng, lat], ...] }

        $this->loadingZipcodes = true;

        $boundaryData = json_decode($boundary, true);

        if ($boundaryData['type'] === 'circle') {
            // Find zipcodes within radius (with 10 mile buffer for partial overlap)
            $center = PostalCode::where('country_code', 'US')
                ->where('latitude', '!=', null)
                ->where('longitude', '!=', null)
                ->selectRaw('
                    postal_code,
                    place_name,
                    admin_name1,
                    (3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
                ', [$boundaryData['center'][1], $boundaryData['center'][0], $boundaryData['center'][1]])
                ->having('distance', '<=', $boundaryData['radius'] + 10) // Add 10 mile buffer
                ->get();

            $this->previewZipcodes = $center->toArray();
        } elseif ($boundaryData['type'] === 'polygon') {
            // Find zipcodes within or near polygon
            // Using buffer distance of ~10 miles to include partially overlapping zipcodes
            $bufferMiles = 10;

            $zipcodes = PostalCode::where('country_code', 'US')
                ->where('latitude', '!=', null)
                ->where('longitude', '!=', null)
                ->get()
                ->filter(function ($zipcode) use ($boundaryData, $bufferMiles) {
                    // Check if point is inside polygon
                    if ($this->isPointInPolygon(
                        $zipcode->longitude,
                        $zipcode->latitude,
                        $boundaryData['coordinates']
                    )) {
                        return true;
                    }

                    // Check if point is within buffer distance of any polygon point
                    // This catches zipcodes that are partially within the polygon
                    $minDistance = $this->getMinDistanceToPolygon(
                        $zipcode->longitude,
                        $zipcode->latitude,
                        $boundaryData['coordinates']
                    );

                    return $minDistance <= $bufferMiles;
                });

            $this->previewZipcodes = $zipcodes->toArray();
        }

        $this->loadingZipcodes = false;

        return count($this->previewZipcodes);
    }

    public function addBoundaryZipcodes()
    {
        if (empty($this->previewZipcodes)) {
            Flux::toast('No zipcodes found in the drawn area.');

            return;
        }

        $added = 0;
        foreach ($this->previewZipcodes as $zipcode) {
            MarketZipcode::firstOrCreate([
                'market_id' => $this->market->id,
                'postal_code' => $zipcode['postal_code'],
            ]);
            $added++;
        }

        $this->previewZipcodes = [];
        $this->drawnBoundary = null;

        Flux::toast("{$added} zipcodes added successfully!");
    }

    private function isPointInPolygon($lng, $lat, $polygon)
    {
        $inside = false;
        $count = count($polygon);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $lat) != ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }

    private function getMinDistanceToPolygon($lng, $lat, $polygon)
    {
        $minDistance = PHP_FLOAT_MAX;

        // Check distance to each vertex of the polygon
        foreach ($polygon as $point) {
            $distance = $this->haversineDistance($lng, $lat, $point[0], $point[1]);
            $minDistance = min($minDistance, $distance);
        }

        // Also check distance to each edge of the polygon
        $count = count($polygon);
        for ($i = 0; $i < $count; $i++) {
            $j = ($i + 1) % $count; // Next vertex (wraps around)
            $edgeDistance = $this->distanceToLineSegment(
                $lng,
                $lat,
                $polygon[$i][0],
                $polygon[$i][1],
                $polygon[$j][0],
                $polygon[$j][1]
            );
            $minDistance = min($minDistance, $edgeDistance);
        }

        return $minDistance;
    }

    private function haversineDistance($lng1, $lat1, $lng2, $lat2)
    {
        $earthRadius = 3959; // miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function distanceToLineSegment($px, $py, $x1, $y1, $x2, $y2)
    {
        // Calculate the distance from point (px, py) to line segment (x1,y1)-(x2,y2)

        $A = $px - $x1;
        $B = $py - $y1;
        $C = $x2 - $x1;
        $D = $y2 - $y1;

        $dot = $A * $C + $B * $D;
        $lenSq = $C * $C + $D * $D;
        $param = ($lenSq != 0) ? $dot / $lenSq : -1;

        if ($param < 0) {
            // Point is closest to start of segment
            return $this->haversineDistance($px, $py, $x1, $y1);
        } elseif ($param > 1) {
            // Point is closest to end of segment
            return $this->haversineDistance($px, $py, $x2, $y2);
        } else {
            // Point is closest to somewhere in the middle of the segment
            $closestX = $x1 + $param * $C;
            $closestY = $y1 + $param * $D;

            return $this->haversineDistance($px, $py, $closestX, $closestY);
        }
    }

    public function render()
    {
        $zipcodes = $this->market->zipcodes()->with('postalCodeDetails')->paginate(20);

        return view('livewire.admin.markets.market-edit', [
            'zipcodes' => $zipcodes,
        ])->layout('layouts.app');
    }
}
