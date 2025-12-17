<?php

namespace App\Livewire\Admin\Users\Tabs;

use App\Models\User;
use Livewire\Component;

class ProfileTab extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user->load(['currentBusiness', 'influencer', 'socialMediaAccounts']);
    }

    public function render()
    {
        return view('livewire.admin.users.tabs.profile-tab');
    }
}
