<?php

namespace App\Livewire\Admin\Businesses\Tabs;

use App\Models\Business;
use Livewire\Component;

class ProfileTab extends Component
{
    public Business $business;

    public function mount(Business $business): void
    {
        $this->business = $business->load(['owner', 'socials']);
    }

    public function render()
    {
        return view('livewire.admin.businesses.tabs.profile-tab');
    }
}
