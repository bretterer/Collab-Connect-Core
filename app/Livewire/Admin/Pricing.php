<?php

namespace App\Livewire\Admin;

use App\Enums\AccountType;
use App\Models\StripePrice;
use App\Subscription\SubscriptionMetadataSchema;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Pricing extends Component
{
    public $showEditModal = false;

    public $selectedPrice = null;

    public $selectedProduct = null;

    public $editType = null; // 'price' or 'product'

    public $priceMetadata = [];

    public $productMetadata = [];

    // Product-specific fields
    public $selectedAccountType = null;

    // Price-specific fields (for features)
    public $priceFeatures = [];

    // Subscription limit fields (for price editing)
    public ?int $activeApplicationsLimit = null;

    public ?int $collaborationLimit = null;

    public ?int $campaignsPublishedLimit = null;

    public ?int $campaignBoostCredits = null;

    public ?int $profilePromotionCredits = null;

    public ?int $teamMemberLimit = null;

    public function mount()
    {
        $this->priceMetadata = [];
        $this->productMetadata = [];
        $this->priceFeatures = [];
        $this->selectedAccountType = null;
        $this->resetLimitFields();
    }

    /**
     * Reset all limit fields to null.
     */
    private function resetLimitFields(): void
    {
        $this->activeApplicationsLimit = null;
        $this->collaborationLimit = null;
        $this->campaignsPublishedLimit = null;
        $this->campaignBoostCredits = null;
        $this->profilePromotionCredits = null;
        $this->teamMemberLimit = null;
    }

    /**
     * Load limit fields from price metadata.
     */
    private function loadLimitFields(): void
    {
        $metadata = $this->priceMetadata;

        $this->activeApplicationsLimit = isset($metadata[SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT])
            ? (int) $metadata[SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT]
            : null;

        $this->collaborationLimit = isset($metadata[SubscriptionMetadataSchema::COLLABORATION_LIMIT])
            ? (int) $metadata[SubscriptionMetadataSchema::COLLABORATION_LIMIT]
            : null;

        $this->campaignsPublishedLimit = isset($metadata[SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT])
            ? (int) $metadata[SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT]
            : null;

        $this->campaignBoostCredits = isset($metadata[SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS])
            ? (int) $metadata[SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS]
            : null;

        $this->profilePromotionCredits = isset($metadata[SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS])
            ? (int) $metadata[SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS]
            : null;

        $this->teamMemberLimit = isset($metadata[SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT])
            ? (int) $metadata[SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT]
            : null;
    }

    /**
     * Get the account type for the currently selected price's product.
     */
    public function getPriceAccountTypeProperty(): ?AccountType
    {
        if (! $this->selectedPrice) {
            return null;
        }

        $product = $this->selectedPrice->product;
        if (! $product) {
            return null;
        }

        $accountTypeValue = $product->metadata['account_type'] ?? null;

        if (! $accountTypeValue) {
            return null;
        }

        foreach (AccountType::cases() as $case) {
            if ($case->name === $accountTypeValue || $case->value == $accountTypeValue) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Get the metadata labels for display.
     *
     * @return array<string, string>
     */
    public function getMetadataLabelsProperty(): array
    {
        return SubscriptionMetadataSchema::getLabels();
    }

    public function render()
    {
        $products = \App\Models\StripeProduct::with(['prices' => function ($query) {
            $query->where('active', true)->orderBy('unit_amount');
        }])
            ->where('active', true)
            ->whereHas('prices', function ($query) {
                $query->where('active', true);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.pricing', [
            'products' => $products,
        ]);
    }

    public function editPrice($priceId)
    {
        $this->selectedPrice = StripePrice::with('product')->findOrFail($priceId);
        $this->priceMetadata = $this->selectedPrice->metadata ?? [];

        // Load existing features from metadata
        $this->priceFeatures = [];
        if (isset($this->priceMetadata['features'])) {
            $features = is_string($this->priceMetadata['features']) ?
                json_decode($this->priceMetadata['features'], true) :
                $this->priceMetadata['features'];
            $this->priceFeatures = $features ?? [];
        }

        // Load limit fields from metadata
        $this->loadLimitFields();

        $this->editType = 'price';
        $this->showEditModal = true;
    }

    public function editProduct($productId)
    {
        $this->selectedProduct = \App\Models\StripeProduct::findOrFail($productId);
        $this->productMetadata = $this->selectedProduct->metadata ?? [];

        // Handle account type - could be stored as string name or integer value
        $accountTypeValue = $this->productMetadata['account_type'] ?? null;

        if ($accountTypeValue) {
            // Try to find the enum by name first (legacy)
            $accountType = null;
            foreach (AccountType::cases() as $case) {
                if ($case->name === $accountTypeValue || $case->value == $accountTypeValue) {
                    $accountType = $case;
                    break;
                }
            }
            $this->selectedAccountType = $accountType?->value;
        } else {
            $this->selectedAccountType = null;
        }

        $this->editType = 'product';
        $this->showEditModal = true;
    }

    public function saveMetadata()
    {
        if ($this->editType === 'price') {
            $this->validate([
                'priceFeatures' => 'array',
                'activeApplicationsLimit' => 'nullable|integer|min:-1',
                'collaborationLimit' => 'nullable|integer|min:-1',
                'campaignsPublishedLimit' => 'nullable|integer|min:-1',
                'campaignBoostCredits' => 'nullable|integer|min:-1',
                'profilePromotionCredits' => 'nullable|integer|min:-1',
                'teamMemberLimit' => 'nullable|integer|min:-1',
            ]);

            try {
                $stripe = Cashier::stripe();

                // Prepare metadata with features
                $metadata = $this->priceMetadata;
                $metadata['features'] = json_encode($this->priceFeatures);

                // Add subscription limit fields to metadata
                if ($this->activeApplicationsLimit !== null) {
                    $metadata[SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT] = (string) $this->activeApplicationsLimit;
                }
                if ($this->collaborationLimit !== null) {
                    $metadata[SubscriptionMetadataSchema::COLLABORATION_LIMIT] = (string) $this->collaborationLimit;
                }
                if ($this->campaignsPublishedLimit !== null) {
                    $metadata[SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT] = (string) $this->campaignsPublishedLimit;
                }
                if ($this->campaignBoostCredits !== null) {
                    $metadata[SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS] = (string) $this->campaignBoostCredits;
                }
                if ($this->profilePromotionCredits !== null) {
                    $metadata[SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS] = (string) $this->profilePromotionCredits;
                }
                if ($this->teamMemberLimit !== null) {
                    $metadata[SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT] = (string) $this->teamMemberLimit;
                }

                $stripe->prices->update($this->selectedPrice->stripe_id, [
                    'metadata' => $metadata,
                ]);

                session()->flash('message', 'Price settings updated successfully. Changes will sync via webhook.');
                $this->closeModal();
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to update price settings: '.$e->getMessage());
            }
        } elseif ($this->editType === 'product') {
            $this->validate([
                'selectedAccountType' => ['required', 'integer', AccountType::validationRule()],
            ]);

            try {
                $stripe = Cashier::stripe();

                // Get the enum case and store its name (for consistency)
                $accountType = AccountType::from($this->selectedAccountType);

                // Prepare metadata with account type
                $metadata = $this->productMetadata;
                $metadata['account_type'] = $accountType->name;

                $stripe->products->update($this->selectedProduct->stripe_id, [
                    'metadata' => $metadata,
                ]);

                session()->flash('message', 'Product account type updated successfully. Changes will sync via webhook.');
                $this->closeModal();
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to update product account type: '.$e->getMessage());
            }
        }
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->selectedPrice = null;
        $this->selectedProduct = null;
        $this->editType = null;
        $this->priceMetadata = [];
        $this->productMetadata = [];
        $this->priceFeatures = [];
        $this->selectedAccountType = null;
        $this->resetLimitFields();
    }

    public function addFeature()
    {
        $this->priceFeatures[] = '';
    }

    public function removeFeature($index)
    {
        unset($this->priceFeatures[$index]);
        $this->priceFeatures = array_values($this->priceFeatures); // Re-index array
    }

    public function getAccountTypeOptionsProperty()
    {
        // Filter out UNDEFINED and ADMIN for product assignment
        $relevantCases = collect(AccountType::cases())
            ->filter(fn ($case) => in_array($case, [AccountType::BUSINESS, AccountType::INFLUENCER]))
            ->toArray();

        return AccountType::toOptions($relevantCases);
    }
}
