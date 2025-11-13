<?php

namespace App\Livewire\Admin\Markets;

use App\Models\MarketWaitlist;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class WaitlistManagement extends Component
{
    use WithPagination;

    public $selectedPostalCodes = [];

    public $groupBy = 'zipcode'; // or 'state'

    public function approveZipcode($postalCode)
    {
        $count = 0;

        // Find all users with this postal code who are not market approved
        $users = User::where('postal_code', $postalCode)
            ->where('market_approved', false)
            ->get();

        foreach ($users as $user) {
            $user->update(['market_approved' => true]);

            // Mark waitlist entry as notified
            $waitlist = MarketWaitlist::where('user_id', $user->id)->first();
            if ($waitlist) {
                $waitlist->markAsNotified();
            }

            $count++;
        }

        Flux::toast("{$count} users approved for zipcode {$postalCode}!");
    }

    public function approveSelected()
    {
        if (empty($this->selectedPostalCodes)) {
            Flux::toast('No zipcodes selected.', variant: 'warning');

            return;
        }

        $totalCount = 0;

        foreach ($this->selectedPostalCodes as $postalCode) {
            $users = User::where('postal_code', $postalCode)
                ->where('market_approved', false)
                ->get();

            foreach ($users as $user) {
                $user->update(['market_approved' => true]);

                $waitlist = MarketWaitlist::where('user_id', $user->id)->first();
                if ($waitlist) {
                    $waitlist->markAsNotified();
                }

                $totalCount++;
            }
        }

        $this->selectedPostalCodes = [];

        Flux::toast("{$totalCount} users approved!");
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

        return view('livewire.admin.markets.waitlist-management', [
            'waitlistData' => $waitlistData,
        ])->layout('layouts.app');
    }
}
