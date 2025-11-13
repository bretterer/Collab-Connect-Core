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
            Flux::toast('No zipcodes selected.', variant: 'warning');

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

        $boundaryData = json_decode($boundary, true);

        if ($boundaryData['type'] === 'circle') {
            // Find zipcodes within radius
            $center = PostalCode::where('country_code', 'US')
                ->where('latitude', '!=', null)
                ->where('longitude', '!=', null)
                ->selectRaw('
                    postal_code,
                    place_name,
                    admin_name1,
                    (3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
                ', [$boundaryData['center'][1], $boundaryData['center'][0], $boundaryData['center'][1]])
                ->having('distance', '<=', $boundaryData['radius'])
                ->get();

            $this->previewZipcodes = $center->toArray();
        } elseif ($boundaryData['type'] === 'polygon') {
            // Find zipcodes within polygon using point-in-polygon algorithm
            $zipcodes = PostalCode::where('country_code', 'US')
                ->where('latitude', '!=', null)
                ->where('longitude', '!=', null)
                ->get()
                ->filter(function ($zipcode) use ($boundaryData) {
                    return $this->isPointInPolygon(
                        $zipcode->longitude,
                        $zipcode->latitude,
                        $boundaryData['coordinates']
                    );
                });

            $this->previewZipcodes = $zipcodes->toArray();
        }

        return count($this->previewZipcodes);
    }

    public function addBoundaryZipcodes()
    {
        if (empty($this->previewZipcodes)) {
            Flux::toast('No zipcodes found in the drawn area.', variant: 'warning');

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

    public function render()
    {
        $zipcodes = $this->market->zipcodes()->with('postalCodeDetails')->paginate(20);

        return view('livewire.admin.markets.market-edit', [
            'zipcodes' => $zipcodes,
        ])->layout('layouts.app');
    }
}
