<?php

namespace App\Livewire\Admin\Users\Tabs;

use App\Models\User;
use Flux\Flux;
use Laravel\Pennant\Feature;
use Livewire\Component;

class FeaturesTab extends Component
{
    public User $user;

    public string $featureSearch = '';

    public string $featureStatusFilter = '';

    public array $features = [];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->loadFeatureStates();
    }

    public function loadFeatureStates(): void
    {
        foreach ($this->getAvailableFeatures() as $feature) {
            $this->features[$feature->key] = $this->isFeatureEnabled($feature->key);
        }
    }

    public function toggleFeature(string $featureKey): void
    {
        $isEnabled = $this->features[$featureKey] ?? false;

        if ($isEnabled) {
            Feature::for($this->user)->activate($featureKey);
            Flux::toast("Feature '{$featureKey}' enabled for user.");
        } else {
            Feature::for($this->user)->deactivate($featureKey);
            Flux::toast("Feature '{$featureKey}' disabled for user.");
        }
    }

    public function isFeatureEnabled(string $featureKey): bool
    {
        return Feature::for($this->user)->active($featureKey);
    }

    public function getAvailableFeatures(): array
    {
        $storedFlags = Feature::defined();

        $featureFlags = [];
        foreach ($storedFlags as $flag) {
            $flagClass = new $flag;
            $featureFlags[$flag] = $flagClass;
        }

        return $featureFlags;
    }

    public function getFilteredFeatures(): array
    {
        $features = $this->getAvailableFeatures();

        // Apply search filter
        if ($this->featureSearch) {
            $search = strtolower($this->featureSearch);
            $features = array_filter($features, function ($feature) use ($search) {
                return str_contains(strtolower($feature->title ?? ''), $search) ||
                       str_contains(strtolower($feature->description ?? ''), $search) ||
                       str_contains(strtolower($feature->key ?? ''), $search);
            });
        }

        // Apply status filter
        if ($this->featureStatusFilter) {
            $features = array_filter($features, function ($feature) {
                $isEnabled = $this->isFeatureEnabled($feature->key);

                return $this->featureStatusFilter === 'enabled' ? $isEnabled : ! $isEnabled;
            });
        }

        return array_values($features);
    }

    public function render()
    {
        return view('livewire.admin.users.tabs.features-tab');
    }
}
