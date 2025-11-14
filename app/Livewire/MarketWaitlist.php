<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class MarketWaitlist extends Component
{
    public function mount()
    {
        $registrationMarkets = app(\App\Settings\RegistrationMarkets::class);
        if (! $registrationMarkets->enabled) {
            return redirect()->route('dashboard');
        }
        $user = auth()->user();

        // Redirect users who shouldn't be on the waitlist page
        if ($user) {
            // Admins should go to dashboard
            if ($user->isAdmin()) {
                return redirect()->route('dashboard');
            }

            // Legacy users (no postal_code) should go to dashboard
            if (! $user->postal_code) {
                return redirect()->route('dashboard');
            }

            // Approved users should go to dashboard
            if ($user->market_approved) {
                return redirect()->route('dashboard');
            }
        }
    }

    public function render()
    {
        $user = auth()->user();
        $waitlistEntry = $user?->marketWaitlist;

        return view('livewire.market-waitlist', [
            'user' => $user,
            'waitlistEntry' => $waitlistEntry,
        ]);
    }
}
