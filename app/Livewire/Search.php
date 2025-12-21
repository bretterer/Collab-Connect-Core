<?php

namespace App\Livewire;

use App\Enums\AccountType;
use App\Enums\BusinessIndustry;
use App\Enums\SocialPlatform;
use App\Services\SearchService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Search extends BaseComponent
{
    use WithPagination;

    public bool $searchTracked = false;

    #[Url(except: '')]
    public string $search = '';

    public function mount(): void
    {
        // Track ViewContent for search page
        MetaPixel::track('ViewContent', [
            'content_type' => 'user_search',
            'content_category' => 'search',
        ]);
    }

    #[Url(except: [])]
    public array $selectedNiches = [];

    #[Url(except: [])]
    public array $selectedPlatforms = [];

    #[Url(except: '')]
    public string $minFollowers = '';

    #[Url(except: '')]
    public string $maxFollowers = '';

    #[Url(except: '')]
    public string $location = '';

    #[Url(except: 'relevance')]
    public string $sortBy = 'relevance';

    #[Url(except: 50)]
    public int $searchRadius = 50;

    #[Url(except: false)]
    public bool $showSavedOnly = false;

    #[Url(except: true)]
    public bool $hideHidden = true;

    public bool $showMobileFilters = false;

    /**
     * Reset pagination when any filter changes.
     */
    public function updated($property): void
    {
        // Extract base property name (e.g., 'selectedPlatforms.0' becomes 'selectedPlatforms')
        $baseProperty = explode('.', $property)[0];

        if (in_array($baseProperty, [
            'search',
            'selectedNiches',
            'selectedPlatforms',
            'minFollowers',
            'maxFollowers',
            'location',
            'sortBy',
            'searchRadius',
            'showSavedOnly',
            'hideHidden',
        ])) {
            $this->resetPage();

            // Track Search event when search is performed (only once per search session)
            if ($baseProperty === 'search' && ! empty($this->search) && ! $this->searchTracked) {
                MetaPixel::track('Search', [
                    'search_string' => $this->search,
                    'content_category' => 'users',
                ]);
                $this->searchTracked = true;
            }
        }

        // Auto-set distance sorting when location is entered
        if ($property === 'location' && $this->isValidZipCode() && $this->sortBy === 'relevance') {
            $this->sortBy = 'distance';
        }
    }

    /**
     * Clear all filters.
     */
    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'selectedNiches',
            'selectedPlatforms',
            'minFollowers',
            'maxFollowers',
            'location',
            'sortBy',
            'searchRadius',
            'showSavedOnly',
        ]);
        $this->hideHidden = true;
        $this->resetPage();
    }

    /**
     * Apply a follower preset.
     */
    public function applyFollowerPreset(?int $min, ?int $max): void
    {
        $this->minFollowers = $min ? (string) $min : '';
        $this->maxFollowers = $max ? (string) $max : '';
        $this->resetPage();
    }

    /**
     * Clear the follower count filter.
     */
    public function clearFollowerFilter(): void
    {
        $this->minFollowers = '';
        $this->maxFollowers = '';
        $this->resetPage();
    }

    /**
     * Remove a specific niche from the filter.
     */
    public function removeNiche(string $niche): void
    {
        $this->selectedNiches = array_values(array_filter(
            $this->selectedNiches,
            fn ($n) => $n !== $niche
        ));
        $this->resetPage();
    }

    /**
     * Remove a specific platform from the filter.
     */
    public function removePlatform(string $platform): void
    {
        $this->selectedPlatforms = array_values(array_filter(
            $this->selectedPlatforms,
            fn ($p) => $p !== $platform
        ));
        $this->resetPage();
    }

    /**
     * Toggle mobile filters panel.
     */
    public function toggleMobileFilters(): void
    {
        $this->showMobileFilters = ! $this->showMobileFilters;
    }

    /**
     * Check if the current location is a valid zip code.
     */
    public function isValidZipCode(): bool
    {
        return preg_match('/^\d{5}$/', $this->location) === 1;
    }

    /**
     * Get the count of active filters.
     */
    #[Computed]
    public function activeFilterCount(): int
    {
        $count = 0;

        if (! empty($this->search)) {
            $count++;
        }
        if (! empty($this->selectedNiches)) {
            $count++;
        }
        if (! empty($this->selectedPlatforms)) {
            $count++;
        }
        if (! empty($this->minFollowers) || ! empty($this->maxFollowers)) {
            $count++;
        }
        if (! empty($this->location)) {
            $count++;
        }

        return $count;
    }

    /**
     * Check if the current user is a business (searching for influencers).
     */
    #[Computed]
    public function isBusinessUser(): bool
    {
        return $this->getAuthenticatedUser()->account_type === AccountType::BUSINESS;
    }

    /**
     * Check if the current user is an influencer (searching for businesses).
     */
    #[Computed]
    public function isInfluencerUser(): bool
    {
        return $this->getAuthenticatedUser()->account_type === AccountType::INFLUENCER;
    }

    /**
     * Get the search target type label.
     */
    #[Computed]
    public function searchTargetLabel(): string
    {
        return $this->isBusinessUser ? 'influencers' : 'businesses';
    }

    /**
     * Get the page title based on user type.
     */
    #[Computed]
    public function pageTitle(): string
    {
        return $this->isBusinessUser ? 'Find Influencers' : 'Find Businesses';
    }

    /**
     * Get the page subtitle based on user type.
     */
    #[Computed]
    public function pageSubtitle(): string
    {
        return $this->isBusinessUser
            ? 'Discover talented creators for your next campaign'
            : 'Find businesses looking for collaboration opportunities';
    }

    /**
     * Get industry options for the filter.
     */
    #[Computed]
    public function industryOptions(): array
    {
        return collect(BusinessIndustry::cases())
            ->map(fn ($industry) => [
                'value' => $industry->value,
                'label' => $industry->label(),
            ])
            ->toArray();
    }

    /**
     * Get platform options for the filter.
     */
    #[Computed]
    public function platformOptions(): array
    {
        return collect(SocialPlatform::cases())
            ->map(fn ($platform) => [
                'value' => $platform->value,
                'label' => $platform->label(),
                'icon' => $platform->getIcon(),
            ])
            ->toArray();
    }

    /**
     * Get filter options from the service.
     */
    #[Computed]
    public function filterOptions(): array
    {
        return SearchService::getFilterOptions();
    }

    public function render()
    {
        $currentUser = $this->getAuthenticatedUser();

        $criteria = [
            'search' => $this->search,
            'selectedNiches' => $this->selectedNiches,
            'selectedPlatforms' => $this->selectedPlatforms,
            'minFollowers' => $this->minFollowers,
            'maxFollowers' => $this->maxFollowers,
            'location' => $this->location,
            'sortBy' => $this->sortBy,
            'searchRadius' => $this->searchRadius,
            'showSavedOnly' => $this->showSavedOnly,
            'hideHidden' => $this->hideHidden,
        ];

        // Determine search type based on current user's account type
        $searchType = $this->isBusinessUser ? 'influencers' : 'businesses';
        $results = SearchService::searchProfiles($searchType, $criteria, $currentUser, 12);
        $metadata = SearchService::getSearchMetadata($criteria);

        // Get counts for the filter badges
        $savedCount = $currentUser->savedUsers()->count();
        $hiddenCount = $currentUser->hiddenUsers()->count();

        return view('livewire.search', [
            'results' => $results,
            'searchPostalCode' => $metadata['searchPostalCode'],
            'isProximitySearch' => $metadata['isProximitySearch'],
            'savedCount' => $savedCount,
            'hiddenCount' => $hiddenCount,
        ]);
    }
}
