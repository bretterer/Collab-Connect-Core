<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class MarketWaitlist extends Component
{
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
