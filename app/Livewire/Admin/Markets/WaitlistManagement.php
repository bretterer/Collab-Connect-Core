<?php

namespace App\Livewire\Admin\Markets;

use App\Models\MarketWaitlist;
use App\Services\MarketService;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class WaitlistManagement extends Component
{
    use WithPagination;

    public $selectedPostalCodes = [];

    public $groupBy = 'zipcode'; // or 'state'

    // Modal state
    public $showApprovalModal = false;

    public $approvalPostalCode = null;

    public $approvalUserCount = 0;

    public $createNewMarket = true;

    public $newMarketName = '';

    public $existingMarketId = null;

    public $drawnBoundary = null;

    public $previewZipcodes = [];

    public $sendNotification = true;

    public function openApprovalModal($postalCode, $userCount)
    {
        $this->approvalPostalCode = $postalCode;
        $this->approvalUserCount = $userCount;
        $this->showApprovalModal = true;

        // Get postal code details for default market name
        $postalCodeDetails = \App\Models\PostalCode::where('postal_code', $postalCode)
            ->where('country_code', 'US')
            ->first();

        if ($postalCodeDetails) {
            $this->newMarketName = $postalCodeDetails->place_name.', '.$postalCodeDetails->admin_name1;
        }

        // Reset other fields
        $this->createNewMarket = true;
        $this->existingMarketId = null;
        $this->drawnBoundary = null;
        $this->previewZipcodes = [];
        $this->sendNotification = true;

        // Dispatch event to initialize map with coordinates
        $coordinates = [
            'lat' => $postalCodeDetails->latitude ?? 39.8283,
            'lng' => $postalCodeDetails->longitude ?? -98.5795,
        ];

        $this->dispatch('approvalModalOpened', $coordinates);
    }

    public function processApproval()
    {
        $this->validate([
            'createNewMarket' => 'required',
            'newMarketName' => 'required_if:createNewMarket,true,1|string|max:255',
            'existingMarketId' => 'nullable|required_if:createNewMarket,false,0|exists:markets,id',
        ]);

        $market = null;

        // Create or use existing market
        if ($this->createNewMarket) {
            $market = \App\Models\Market::create([
                'name' => $this->newMarketName,
                'description' => 'Created from waitlist approval',
                'is_active' => true,
            ]);
        } else {
            $market = \App\Models\Market::find($this->existingMarketId);
        }

        // Add the primary zipcode
        \App\Models\MarketZipcode::firstOrCreate([
            'market_id' => $market->id,
            'postal_code' => $this->approvalPostalCode,
        ]);

        // Collect all zipcodes (primary + drawn boundary)
        $allZipcodes = [$this->approvalPostalCode];

        if (! empty($this->previewZipcodes)) {
            foreach ($this->previewZipcodes as $zipcode) {
                \App\Models\MarketZipcode::firstOrCreate([
                    'market_id' => $market->id,
                    'postal_code' => $zipcode['postal_code'],
                ]);
                $allZipcodes[] = $zipcode['postal_code'];
            }
        }

        // Approve users from ALL zipcodes using MarketService (handles subscriptions via job)
        $marketService = app(MarketService::class);
        $result = $marketService->approveWaitlistedUsersForPostalCodes(
            $allZipcodes,
            $market,
            $this->sendNotification
        );
        $totalUsers = $result['approved_count'];

        $this->showApprovalModal = false;

        Flux::toast("Market '{$market->name}' created with ".count($allZipcodes).' '.Str::plural('zipcode', count($allZipcodes)).'. '.$totalUsers.' '.Str::plural('user', $totalUsers).' approved!');

        // Refresh the component
        $this->reset(['approvalPostalCode', 'approvalUserCount', 'newMarketName', 'existingMarketId', 'drawnBoundary', 'previewZipcodes']);
    }

    public function findZipcodesInBoundary($boundary)
    {
        $boundaryData = json_decode($boundary, true);

        if ($boundaryData['type'] === 'circle') {
            $center = \App\Models\PostalCode::where('country_code', 'US')
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
            $zipcodes = \App\Models\PostalCode::where('country_code', 'US')
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

    public function approveSelected()
    {
        if (empty($this->selectedPostalCodes)) {
            Flux::toast('No zipcodes selected.');

            return;
        }

        // Approve users using MarketService (handles subscriptions via job)
        $marketService = app(MarketService::class);
        $result = $marketService->approveWaitlistedUsersForPostalCodes(
            $this->selectedPostalCodes,
            null,
            false
        );

        $this->selectedPostalCodes = [];

        Flux::toast("{$result['approved_count']} users approved!");
    }

    public function togglePostalCodeSelection($postalCode)
    {
        if (in_array($postalCode, $this->selectedPostalCodes)) {
            $this->selectedPostalCodes = array_diff($this->selectedPostalCodes, [$postalCode]);
        } else {
            $this->selectedPostalCodes[] = $postalCode;
        }
    }

    public function render()
    {
        $waitlistData = MarketWaitlist::select('postal_code', DB::raw('count(*) as user_count'))
            ->whereHas('user', fn ($query) => $query->where('market_approved', false))
            ->groupBy('postal_code')
            ->orderByDesc('user_count')
            ->paginate(20);

        // Get postal code details for each
        $waitlistData->each(function ($item) {
            $item->postal_code_details = \App\Models\PostalCode::where('postal_code', $item->postal_code)
                ->where('country_code', 'US')
                ->first();
        });

        // Get all waitlist zipcodes with coordinates for map (not paginated)
        $mapData = MarketWaitlist::select('postal_code', DB::raw('count(*) as user_count'))
            ->whereHas('user', fn ($query) => $query->where('market_approved', false))
            ->groupBy('postal_code')
            ->get()
            ->map(function ($item) {
                $postalCodeDetails = \App\Models\PostalCode::where('postal_code', $item->postal_code)
                    ->where('country_code', 'US')
                    ->first();

                if ($postalCodeDetails && $postalCodeDetails->latitude && $postalCodeDetails->longitude) {
                    return [
                        'postal_code' => $item->postal_code,
                        'user_count' => $item->user_count,
                        'latitude' => $postalCodeDetails->latitude,
                        'longitude' => $postalCodeDetails->longitude,
                        'place_name' => $postalCodeDetails->place_name,
                        'admin_name1' => $postalCodeDetails->admin_name1,
                    ];
                }

                return null;
            })
            ->filter()
            ->values();

        // Get all markets for dropdown
        $markets = \App\Models\Market::orderBy('name')
            ->get();

        // Get postal code details for modal map centering
        $approvalPostalCodeDetails = null;
        if ($this->approvalPostalCode) {
            $approvalPostalCodeDetails = \App\Models\PostalCode::where('postal_code', $this->approvalPostalCode)
                ->where('country_code', 'US')
                ->first();
        }

        return view('livewire.admin.markets.waitlist-management', [
            'waitlistData' => $waitlistData,
            'mapData' => $mapData,
            'markets' => $markets,
            'approvalPostalCodeDetails' => $approvalPostalCodeDetails,
        ])->layout('layouts.app');
    }
}
