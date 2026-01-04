<?php

namespace App\Livewire\Marketing;

use App\Enums\AccountType;
use App\Models\StripeProduct;
use App\Settings\PricingMatrixSettings;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.marketing')]
class Pricing extends Component
{
    public string $activeTab = 'influencer';

    #[Computed]
    public function settings(): PricingMatrixSettings
    {
        return app(PricingMatrixSettings::class);
    }

    #[Computed]
    public function businessCategories(): array
    {
        return $this->settings->business_categories;
    }

    #[Computed]
    public function influencerCategories(): array
    {
        return $this->settings->influencer_categories;
    }

    #[Computed]
    public function businessPrices(): \Illuminate\Support\Collection
    {
        return StripeProduct::query()
            ->where('active', true)
            ->whereNull('deleted_at')
            ->whereJsonContains('metadata->account_type', AccountType::BUSINESS->name)
            ->with(['prices' => function ($query) {
                $query->where('active', true)
                    ->whereNull('deleted_at')
                    ->whereNotNull('recurring')
                    ->orderBy('unit_amount');
            }])
            ->get()
            ->flatMap(fn ($product) => $product->prices)
            ->sortBy('unit_amount');
    }

    #[Computed]
    public function influencerPrices(): \Illuminate\Support\Collection
    {
        return StripeProduct::query()
            ->where('active', true)
            ->whereNull('deleted_at')
            ->whereJsonContains('metadata->account_type', AccountType::INFLUENCER->name)
            ->with(['prices' => function ($query) {
                $query->where('active', true)
                    ->whereNull('deleted_at')
                    ->whereNotNull('recurring')
                    ->orderBy('unit_amount');
            }])
            ->get()
            ->flatMap(fn ($product) => $product->prices)
            ->sortBy('unit_amount');
    }

    #[Computed]
    public function highlightedBusinessPriceId(): ?string
    {
        return $this->settings->highlighted_business_price_id;
    }

    #[Computed]
    public function highlightedInfluencerPriceId(): ?string
    {
        return $this->settings->highlighted_influencer_price_id;
    }

    #[Computed]
    public function hasBusinessFeatures(): bool
    {
        foreach ($this->businessCategories as $category) {
            if (! empty($category['features'])) {
                return true;
            }
        }

        return false;
    }

    #[Computed]
    public function hasInfluencerFeatures(): bool
    {
        foreach ($this->influencerCategories as $category) {
            if (! empty($category['features'])) {
                return true;
            }
        }

        return false;
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.marketing.pricing')
            ->title('Pricing - CollabConnect')
            ->layoutData([
                'description' => 'Simple, transparent pricing for businesses and influencers. Choose the plan that fits your needs.',
            ]);
    }
}
