<?php

namespace App\Livewire\Admin\Influencers\Tabs;

use App\Models\Influencer;
use Livewire\Component;

class ProfileTab extends Component
{
    public Influencer $influencer;

    public function mount(Influencer $influencer): void
    {
        $this->influencer = $influencer->load(['user', 'user.socialMediaAccounts']);
    }

    public function render()
    {
        return view('livewire.admin.influencers.tabs.profile-tab');
    }
}
