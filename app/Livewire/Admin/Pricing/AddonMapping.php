<?php

namespace App\Livewire\Admin\Pricing;

use App\Facades\AddonPricing;
use App\Models\AddonPriceMapping;
use App\Models\StripePrice;
use App\Subscription\SubscriptionMetadataSchema;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class AddonMapping extends Component
{
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    // Create form fields
    public ?int $selectedPriceId = null;

    public string $selectedCreditKey = '';

    public int $creditsGranted = 1;

    public string $accountType = 'both';

    public bool $isActive = true;

    public int $sortOrder = 0;

    public string $displayName = '';

    // Edit form
    public ?int $editingMappingId = null;

    #[Computed]
    public function mappings()
    {
        return AddonPriceMapping::query()
            ->with('stripePrice.product')
            ->orderBy('credit_key')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('credit_key');
    }

    #[Computed]
    public function availablePrices()
    {
        return AddonPricing::getAvailableOneTimePrices();
    }

    #[Computed]
    public function creditKeyOptions(): array
    {
        $labels = SubscriptionMetadataSchema::getLabels();

        // Only show credit keys that can be purchased (not hard limits)
        $purchasableKeys = array_merge(
            SubscriptionMetadataSchema::getCreditKeys(),
            SubscriptionMetadataSchema::getOneTimeGrantKeys()
        );

        return collect($purchasableKeys)
            ->unique()
            ->map(fn ($key) => ['value' => $key, 'label' => $labels[$key] ?? $key])
            ->values()
            ->toArray();
    }

    #[Computed]
    public function accountTypeOptions(): array
    {
        return [
            ['value' => 'both', 'label' => 'Both'],
            ['value' => 'business', 'label' => 'Business Only'],
            ['value' => 'influencer', 'label' => 'Influencer Only'],
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function resetCreateForm(): void
    {
        $this->selectedPriceId = null;
        $this->selectedCreditKey = '';
        $this->creditsGranted = 1;
        $this->accountType = 'both';
        $this->isActive = true;
        $this->sortOrder = 0;
        $this->displayName = '';
    }

    public function createMapping(): void
    {
        $this->validate([
            'selectedPriceId' => 'required|exists:stripe_prices,id',
            'selectedCreditKey' => 'required|string',
            'creditsGranted' => 'required|integer|min:1|max:100',
            'accountType' => 'required|in:both,business,influencer',
            'sortOrder' => 'nullable|integer|min:0',
            'displayName' => 'nullable|string|max:255',
        ]);

        $price = StripePrice::findOrFail($this->selectedPriceId);

        AddonPricing::createMapping(
            $price,
            $this->selectedCreditKey,
            $this->creditsGranted,
            $this->accountType,
            [
                'is_active' => $this->isActive,
                'sort_order' => $this->sortOrder ?? 0,
                'display_name' => $this->displayName ?: null,
            ]
        );

        $this->showCreateModal = false;
        $this->resetCreateForm();
        unset($this->mappings);
        Toaster::success('Addon price mapping created successfully.');
    }

    public function editMapping(int $id): void
    {
        $mapping = AddonPriceMapping::with('stripePrice')->findOrFail($id);

        $this->editingMappingId = $id;
        $this->selectedPriceId = $mapping->stripe_price_id;
        $this->selectedCreditKey = $mapping->credit_key;
        $this->creditsGranted = $mapping->credits_granted;
        $this->accountType = $mapping->account_type;
        $this->isActive = $mapping->is_active;
        $this->sortOrder = $mapping->sort_order;
        $this->displayName = $mapping->display_name ?? '';

        $this->showEditModal = true;
    }

    public function updateMapping(): void
    {
        $this->validate([
            'creditsGranted' => 'required|integer|min:1|max:100',
            'accountType' => 'required|in:both,business,influencer',
            'sortOrder' => 'nullable|integer|min:0',
            'displayName' => 'nullable|string|max:255',
        ]);

        $mapping = AddonPriceMapping::findOrFail($this->editingMappingId);

        $mapping->update([
            'credits_granted' => $this->creditsGranted,
            'account_type' => $this->accountType,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder ?? 0,
            'display_name' => $this->displayName ?: null,
        ]);

        $this->showEditModal = false;
        $this->editingMappingId = null;
        unset($this->mappings);
        Toaster::success('Mapping updated successfully.');
    }

    public function toggleActive(int $id): void
    {
        $mapping = AddonPriceMapping::findOrFail($id);
        $mapping->update(['is_active' => ! $mapping->is_active]);
        unset($this->mappings);
        Toaster::success('Mapping status updated.');
    }

    public function deleteMapping(int $id): void
    {
        AddonPriceMapping::findOrFail($id)->delete();
        unset($this->mappings);
        Toaster::success('Mapping deleted.');
    }

    public function render()
    {
        return view('livewire.admin.pricing.addon-mapping');
    }
}
