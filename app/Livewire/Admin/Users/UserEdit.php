<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserEdit extends Component
{
    public User $user;

    public string $activeTab = 'account';

    protected array $validTabs = ['account', 'profile', 'features'];

    public function mount(User $user, ?string $tab = null): void
    {
        $this->user = $user;

        // Handle URL tab parameter
        if ($tab && in_array($tab, $this->validTabs)) {
            $this->activeTab = $tab;
        }
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, $this->validTabs)) {
            $this->activeTab = $tab;
            $this->updateUrl();
        }
    }

    protected function updateUrl(): void
    {
        $url = '/admin/users/'.$this->user->id.'/edit/'.$this->activeTab;
        $this->dispatch('update-url', url: $url);
    }

    public function render()
    {
        return view('livewire.admin.users.user-edit');
    }
}
