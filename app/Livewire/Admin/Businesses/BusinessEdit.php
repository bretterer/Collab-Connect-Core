<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BusinessEdit extends Component
{
    public Business $business;

    public string $activeTab = 'profile';

    protected array $validTabs = ['profile', 'billing'];

    public function mount(Business $business, ?string $tab = null): void
    {
        $this->business = $business->load(['owner', 'subscriptions']);

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
        $url = '/admin/businesses/'.$this->business->id.'/edit/'.$this->activeTab;
        $this->dispatch('update-url', url: $url);
    }

    public function render()
    {
        return view('livewire.admin.businesses.business-edit');
    }
}
