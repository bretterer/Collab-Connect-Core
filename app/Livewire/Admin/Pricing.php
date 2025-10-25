<?php

namespace App\Livewire\Admin;

use App\Models\StripePrice;
use App\Enums\AccountType;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Laravel\Cashier\Cashier;

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

    public function mount()
    {
        $this->priceMetadata = [];
        $this->productMetadata = [];
        $this->priceFeatures = [];
        $this->selectedAccountType = null;
    }

    public function render()
    {
        $products = \App\Models\StripeProduct::with(['prices' => function($query) {
                $query->where('active', true)->orderBy('unit_amount');
            }])
            ->where('active', true)
            ->whereHas('prices', function($query) {
                $query->where('active', true);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.pricing', [
            'products' => $products
        ]);
    }

    public function editPrice($priceId)
    {
        $this->selectedPrice = StripePrice::findOrFail($priceId);
        $this->priceMetadata = $this->selectedPrice->metadata ?? [];
        
        // Load existing features from metadata
        $this->priceFeatures = [];
        if (isset($this->priceMetadata['features'])) {
            $features = is_string($this->priceMetadata['features']) ? 
                json_decode($this->priceMetadata['features'], true) : 
                $this->priceMetadata['features'];
            $this->priceFeatures = $features ?? [];
        }
        
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
            ]);

            try {
                $stripe = Cashier::stripe();
                
                // Prepare metadata with features
                $metadata = $this->priceMetadata;
                $metadata['features'] = json_encode($this->priceFeatures);
                
                $stripe->prices->update($this->selectedPrice->stripe_id, [
                    'metadata' => $metadata
                ]);

                session()->flash('message', 'Price features updated successfully. Changes will sync via webhook.');
                $this->closeModal();
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to update price features: ' . $e->getMessage());
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
                    'metadata' => $metadata
                ]);

                session()->flash('message', 'Product account type updated successfully. Changes will sync via webhook.');
                $this->closeModal();
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to update product account type: ' . $e->getMessage());
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
            ->filter(fn($case) => in_array($case, [AccountType::BUSINESS, AccountType::INFLUENCER]))
            ->toArray();
            
        return AccountType::toOptions($relevantCases);
    }
}
