<?php

namespace App\Livewire;

use App\Enums\AccountType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function mount()
    {
        // Redirect admin users to their dedicated dashboard
        if (Auth::user()->account_type === AccountType::ADMIN) {
            return redirect()->route('admin.dashboard');
        }

        // Redirect business users to business dashboard
        if (Auth::user()->account_type === AccountType::BUSINESS) {
            return redirect()->route('business.dashboard');
        }

        // Redirect influencer users to influencer dashboard
        if (Auth::user()->account_type === AccountType::INFLUENCER) {
            return redirect()->route('influencer.dashboard');
        }

        // Fallback for any other account types
        abort(403, 'Invalid account type');
    }

    public function render()
    {
        // This render method should not be reached due to redirects in mount()
        return view('livewire.dashboard');
    }

    // All dashboard logic has been moved to specialized components:
    // - BusinessDashboard for business users
    // - InfluencerDashboard for influencer users
    // This component now only handles routing to the appropriate dashboard
}
