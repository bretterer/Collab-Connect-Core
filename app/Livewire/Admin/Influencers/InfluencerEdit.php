<?php

namespace App\Livewire\Admin\Influencers;

use App\Models\Influencer;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class InfluencerEdit extends Component
{
    public Influencer $influencer;

    public string $activeTab = 'profile';

    protected array $validTabs = ['profile', 'billing'];

    public function mount(Influencer $influencer, ?string $tab = null): void
    {
        $this->influencer = $influencer->load(['user', 'subscriptions']);

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
        $url = '/admin/influencers/'.$this->influencer->id.'/edit/'.$this->activeTab;
        $this->dispatch('update-url', url: $url);
    }

    public function render()
    {
        return view('livewire.admin.influencers.influencer-edit');
    }
}
