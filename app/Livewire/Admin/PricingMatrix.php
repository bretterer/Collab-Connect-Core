<?php

namespace App\Livewire\Admin;

use App\Enums\AccountType;
use App\Models\StripePrice;
use App\Settings\PricingMatrixSettings;
use Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PricingMatrix extends Component
{
    public string $activeTab = 'business';

    public bool $showCategoryModal = false;

    public bool $showFeatureModal = false;

    public ?int $editingCategoryIndex = null;

    public ?int $editingFeatureIndex = null;

    public ?string $editingCategoryType = null;

    public string $categoryKey = '';

    public string $categoryLabel = '';

    public string $featureKey = '';

    public string $featureLabel = '';

    public string $featureType = 'boolean';

    public string $featureDescription = '';

    public ?string $highlightedBusinessPriceId = null;

    public ?string $highlightedInfluencerPriceId = null;

    public function mount(PricingMatrixSettings $settings): void
    {
        $this->highlightedBusinessPriceId = $settings->highlighted_business_price_id;
        $this->highlightedInfluencerPriceId = $settings->highlighted_influencer_price_id;
    }

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
    public function businessPrices(): \Illuminate\Database\Eloquent\Collection
    {
        return StripePrice::query()
            ->whereHas('product', function ($query) {
                $query->where('active', true)
                    ->whereJsonContains('metadata->account_type', AccountType::BUSINESS->name);
            })
            ->where('active', true)
            ->whereNotNull('recurring')
            ->with('product')
            ->orderBy('unit_amount')
            ->get();
    }

    #[Computed]
    public function influencerPrices(): \Illuminate\Database\Eloquent\Collection
    {
        return StripePrice::query()
            ->whereHas('product', function ($query) {
                $query->where('active', true)
                    ->whereJsonContains('metadata->account_type', AccountType::INFLUENCER->name);
            })
            ->where('active', true)
            ->whereNotNull('recurring')
            ->with('product')
            ->orderBy('unit_amount')
            ->get();
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openAddCategoryModal(): void
    {
        $this->resetCategoryForm();
        $this->editingCategoryType = $this->activeTab;
        $this->showCategoryModal = true;
    }

    public function openEditCategoryModal(int $index): void
    {
        $categories = $this->activeTab === 'business'
            ? $this->settings->business_categories
            : $this->settings->influencer_categories;

        if (isset($categories[$index])) {
            $this->editingCategoryIndex = $index;
            $this->editingCategoryType = $this->activeTab;
            $this->categoryKey = $categories[$index]['key'];
            $this->categoryLabel = $categories[$index]['label'];
            $this->showCategoryModal = true;
        }
    }

    public function saveCategory(): void
    {
        $this->validate([
            'categoryKey' => 'required|string|max:50|regex:/^[a-z0-9_]+$/',
            'categoryLabel' => 'required|string|max:100',
        ], [
            'categoryKey.regex' => 'Category key must contain only lowercase letters, numbers, and underscores.',
        ]);

        $settings = $this->settings;
        $categoriesProperty = $this->editingCategoryType === 'business'
            ? 'business_categories'
            : 'influencer_categories';

        $categories = $settings->$categoriesProperty;

        if ($this->editingCategoryIndex !== null) {
            // Update existing category
            $categories[$this->editingCategoryIndex]['key'] = $this->categoryKey;
            $categories[$this->editingCategoryIndex]['label'] = $this->categoryLabel;
        } else {
            // Add new category
            $categories[] = [
                'key' => $this->categoryKey,
                'label' => $this->categoryLabel,
                'features' => [],
            ];
        }

        $settings->$categoriesProperty = $categories;
        $settings->save();

        $this->closeCategoryModal();
        Flux::toast('Category saved successfully!', variant: 'success');
    }

    public function deleteCategory(int $index): void
    {
        $settings = $this->settings;
        $categoriesProperty = $this->activeTab === 'business'
            ? 'business_categories'
            : 'influencer_categories';

        $categories = $settings->$categoriesProperty;
        unset($categories[$index]);
        $settings->$categoriesProperty = array_values($categories);
        $settings->save();

        Flux::toast('Category deleted successfully!', variant: 'success');
    }

    public function closeCategoryModal(): void
    {
        $this->showCategoryModal = false;
        $this->resetCategoryForm();
    }

    protected function resetCategoryForm(): void
    {
        $this->editingCategoryIndex = null;
        $this->editingCategoryType = null;
        $this->categoryKey = '';
        $this->categoryLabel = '';
    }

    public function openAddFeatureModal(int $categoryIndex): void
    {
        $this->resetFeatureForm();
        $this->editingCategoryIndex = $categoryIndex;
        $this->editingCategoryType = $this->activeTab;
        $this->showFeatureModal = true;
    }

    public function openEditFeatureModal(int $categoryIndex, int $featureIndex): void
    {
        $categories = $this->activeTab === 'business'
            ? $this->settings->business_categories
            : $this->settings->influencer_categories;

        if (isset($categories[$categoryIndex]['features'][$featureIndex])) {
            $feature = $categories[$categoryIndex]['features'][$featureIndex];
            $this->editingCategoryIndex = $categoryIndex;
            $this->editingFeatureIndex = $featureIndex;
            $this->editingCategoryType = $this->activeTab;
            $this->featureKey = $feature['key'];
            $this->featureLabel = $feature['label'];
            $this->featureType = $feature['type'];
            $this->featureDescription = $feature['description'] ?? '';
            $this->showFeatureModal = true;
        }
    }

    public function saveFeature(): void
    {
        $this->validate([
            'featureKey' => 'required|string|max:50|regex:/^[a-z0-9_]+$/',
            'featureLabel' => 'required|string|max:100',
            'featureType' => 'required|in:boolean,number,text',
            'featureDescription' => 'nullable|string|max:500',
        ], [
            'featureKey.regex' => 'Feature key must contain only lowercase letters, numbers, and underscores.',
        ]);

        $settings = $this->settings;
        $categoriesProperty = $this->editingCategoryType === 'business'
            ? 'business_categories'
            : 'influencer_categories';

        $categories = $settings->$categoriesProperty;

        if ($this->editingCategoryIndex !== null && isset($categories[$this->editingCategoryIndex])) {
            $feature = [
                'key' => $this->featureKey,
                'label' => $this->featureLabel,
                'type' => $this->featureType,
                'description' => $this->featureDescription,
            ];

            if ($this->editingFeatureIndex !== null) {
                // Update existing feature
                $categories[$this->editingCategoryIndex]['features'][$this->editingFeatureIndex] = $feature;
            } else {
                // Add new feature
                $categories[$this->editingCategoryIndex]['features'][] = $feature;
            }

            $settings->$categoriesProperty = $categories;
            $settings->save();
        }

        $this->closeFeatureModal();
        Flux::toast('Feature saved successfully!', variant: 'success');
    }

    public function deleteFeature(int $categoryIndex, int $featureIndex): void
    {
        $settings = $this->settings;
        $categoriesProperty = $this->activeTab === 'business'
            ? 'business_categories'
            : 'influencer_categories';

        $categories = $settings->$categoriesProperty;

        if (isset($categories[$categoryIndex]['features'][$featureIndex])) {
            unset($categories[$categoryIndex]['features'][$featureIndex]);
            $categories[$categoryIndex]['features'] = array_values($categories[$categoryIndex]['features']);
            $settings->$categoriesProperty = $categories;
            $settings->save();

            Flux::toast('Feature deleted successfully!', variant: 'success');
        }
    }

    public function closeFeatureModal(): void
    {
        $this->showFeatureModal = false;
        $this->resetFeatureForm();
    }

    protected function resetFeatureForm(): void
    {
        $this->editingCategoryIndex = null;
        $this->editingFeatureIndex = null;
        $this->editingCategoryType = null;
        $this->featureKey = '';
        $this->featureLabel = '';
        $this->featureType = 'boolean';
        $this->featureDescription = '';
    }

    public function moveCategoryUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $settings = $this->settings;
        $categoriesProperty = $this->activeTab === 'business'
            ? 'business_categories'
            : 'influencer_categories';

        $categories = $settings->$categoriesProperty;
        $temp = $categories[$index - 1];
        $categories[$index - 1] = $categories[$index];
        $categories[$index] = $temp;

        $settings->$categoriesProperty = $categories;
        $settings->save();
    }

    public function moveCategoryDown(int $index): void
    {
        $settings = $this->settings;
        $categoriesProperty = $this->activeTab === 'business'
            ? 'business_categories'
            : 'influencer_categories';

        $categories = $settings->$categoriesProperty;

        if ($index >= count($categories) - 1) {
            return;
        }

        $temp = $categories[$index + 1];
        $categories[$index + 1] = $categories[$index];
        $categories[$index] = $temp;

        $settings->$categoriesProperty = $categories;
        $settings->save();
    }

    public function moveFeatureUp(int $categoryIndex, int $featureIndex): void
    {
        if ($featureIndex <= 0) {
            return;
        }

        $settings = $this->settings;
        $categoriesProperty = $this->activeTab === 'business'
            ? 'business_categories'
            : 'influencer_categories';

        $categories = $settings->$categoriesProperty;
        $features = $categories[$categoryIndex]['features'];

        $temp = $features[$featureIndex - 1];
        $features[$featureIndex - 1] = $features[$featureIndex];
        $features[$featureIndex] = $temp;

        $categories[$categoryIndex]['features'] = $features;
        $settings->$categoriesProperty = $categories;
        $settings->save();
    }

    public function moveFeatureDown(int $categoryIndex, int $featureIndex): void
    {
        $settings = $this->settings;
        $categoriesProperty = $this->activeTab === 'business'
            ? 'business_categories'
            : 'influencer_categories';

        $categories = $settings->$categoriesProperty;
        $features = $categories[$categoryIndex]['features'];

        if ($featureIndex >= count($features) - 1) {
            return;
        }

        $temp = $features[$featureIndex + 1];
        $features[$featureIndex + 1] = $features[$featureIndex];
        $features[$featureIndex] = $temp;

        $categories[$categoryIndex]['features'] = $features;
        $settings->$categoriesProperty = $categories;
        $settings->save();
    }

    public function saveHighlightedPlan(): void
    {
        $settings = $this->settings;

        $settings->highlighted_business_price_id = $this->highlightedBusinessPriceId ?: null;
        $settings->highlighted_influencer_price_id = $this->highlightedInfluencerPriceId ?: null;
        $settings->save();

        Flux::toast('Highlighted plans updated successfully!', variant: 'success');
    }

    public function render()
    {
        return view('livewire.admin.pricing-matrix');
    }
}
