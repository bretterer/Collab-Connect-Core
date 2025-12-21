<?php

namespace App\Livewire\Campaigns;

use App\Enums\AccountType;
use App\Enums\BusinessIndustry;
use App\Enums\CampaignStatus;
use App\Facades\MatchScore;
use App\Livewire\BaseComponent;
use App\Models\Campaign;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class InfluencerCampaigns extends BaseComponent
{
    use WithPagination;

    public string $search = '';

    public array $selectedNiches = [];

    public array $selectedCampaignTypes = [];

    public string $sortBy = 'match_score';

    public int $searchRadius = 50;

    public int $perPage = 12;

    public string $activeTab = 'all';

    public ?int $quickViewCampaignId = null;

    public bool $showQuickViewModal = false;

    /**
     * Cached saved campaign IDs to avoid multiple queries per request
     */
    private ?array $cachedSavedCampaignIds = null;

    /**
     * Cached hidden campaign IDs to avoid multiple queries per request
     */
    private ?array $cachedHiddenCampaignIds = null;

    /**
     * Cached user campaign applications keyed by campaign_id
     */
    private ?\Illuminate\Support\Collection $cachedUserApplications = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedNiches' => ['except' => []],
        'selectedCampaignTypes' => ['except' => []],
        'sortBy' => ['except' => 'match_score'],
        'perPage' => ['except' => 12],
        'activeTab' => ['except' => 'all'],
    ];

    public function mount()
    {
        // redirect business users to their campaigns
        if (Auth::user()->account_type === AccountType::BUSINESS) {
            return redirect()->route('campaigns.index');
        }
        // URL parameters will be automatically loaded due to $queryString property

        // Track ViewContent for campaign discovery page
        MetaPixel::track('ViewContent', [
            'content_type' => 'campaign_discovery',
            'content_category' => 'campaigns',
        ]);
    }

    public function getOpenCampaigns()
    {
        $user = Auth::user();
        $influencerProfile = $user->influencer;

        if (! $influencerProfile) {
            return collect();
        }

        // Get hidden campaign IDs to exclude
        $hiddenCampaignIds = $this->getHiddenCampaignIds();

        // Handle saved campaigns tab
        if ($this->activeTab === 'saved') {
            $savedCampaignIds = $this->getSavedCampaignIds();

            $query = Campaign::query()
                ->whereIn('id', $savedCampaignIds)
                ->where('status', CampaignStatus::PUBLISHED)
                ->where('application_deadline', '>', now())
                ->with(['business']);
        } elseif ($this->activeTab === 'hidden') {
            // Show hidden campaigns so user can unhide them
            $query = Campaign::query()
                ->whereIn('id', $hiddenCampaignIds)
                ->with(['business']);
        } else {
            // All campaigns tab - exclude hidden
            $query = Campaign::query()
                ->where('status', CampaignStatus::PUBLISHED)
                ->where('application_deadline', '>', now())
                ->whereNotIn('id', $hiddenCampaignIds)
                ->with(['business']);

            // Exclude user's own business campaigns if they have a business
            if ($user->current_business) {
                $query->where('business_id', '!=', $user->current_business);
            }
        }

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('campaign_goal', 'like', '%'.$this->search.'%')
                    ->orWhere('campaign_description', 'like', '%'.$this->search.'%');
            });
        }

        // Apply niche filter (not for hidden tab)
        if (! empty($this->selectedNiches) && $this->activeTab !== 'hidden') {
            $query->whereHas('business', function ($q) {
                $q->whereIn('industry', $this->selectedNiches);
            });
        }

        // Apply campaign type filter (not for hidden tab)
        if (! empty($this->selectedCampaignTypes) && $this->activeTab !== 'hidden') {
            $query->whereIn('campaign_type', $this->selectedCampaignTypes);
        }

        $campaigns = $query->get();

        // Pre-load all postal codes in a single query to avoid N+1
        MatchScore::preloadPostalCodes($campaigns, $influencerProfile);

        // Calculate match scores and sort
        $campaigns = $campaigns->map(function ($campaign) use ($influencerProfile) {
            $campaign->match_score = MatchScore::calculateMatchScore($campaign, $influencerProfile, $this->searchRadius);

            return $campaign;
        });

        // Sort by match score or other criteria
        $campaigns = $this->sortCampaigns($campaigns);

        // Return the collection for Livewire pagination
        return $campaigns;
    }

    private function sortCampaigns($campaigns)
    {
        return match ($this->sortBy) {
            'match_score' => $campaigns->sortByDesc('match_score'),
            'newest' => $campaigns->sortByDesc('published_at'),
            'compensation' => $campaigns->sortByDesc('compensation_amount'),
            'deadline' => $campaigns->sortBy('application_deadline'),
            default => $campaigns->sortByDesc('match_score'),
        };
    }

    public function getNicheOptions(): array
    {
        return BusinessIndustry::cases();
    }

    public function getCampaignTypeOptions(): array
    {
        return \App\Enums\CampaignType::forInfluencers();
    }

    public function updatedSearch()
    {
        $this->resetPage();

        // Track Search event when search query changes
        if (! empty($this->search)) {
            MetaPixel::track('Search', [
                'search_string' => $this->search,
                'content_category' => 'campaigns',
            ]);
        }
    }

    public function updatedSelectedNiches()
    {
        $this->resetPage();
    }

    public function updatedSelectedCampaignTypes()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function nextPage()
    {
        $this->setPage($this->getPage() + 1);
    }

    public function previousPage()
    {
        $this->setPage($this->getPage() - 1);
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedNiches = [];
        $this->selectedCampaignTypes = [];
        $this->sortBy = 'match_score';
        $this->perPage = 12;
        $this->resetPage();
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function toggleSaveCampaign(int $campaignId): void
    {
        $user = Auth::user();
        $campaign = Campaign::find($campaignId);

        if (! $campaign) {
            return;
        }

        if ($user->hasSavedCampaign($campaign)) {
            $user->savedCampaigns()->detach($campaignId);
            Flux::toast('Campaign removed from saved list');
        } else {
            $user->savedCampaigns()->attach($campaignId);
            Flux::toast('Campaign saved for later');
        }

        $this->clearCaches();
    }

    public function hideCampaign(int $campaignId): void
    {
        $user = Auth::user();
        $campaign = Campaign::find($campaignId);

        if (! $campaign) {
            return;
        }

        if (! $user->hasHiddenCampaign($campaign)) {
            $user->hiddenCampaigns()->attach($campaignId);
            // Also remove from saved if it was saved
            $user->savedCampaigns()->detach($campaignId);
            Flux::toast('Campaign hidden from your feed');
        }

        $this->clearCaches();
    }

    public function unhideCampaign(int $campaignId): void
    {
        $user = Auth::user();
        $user->hiddenCampaigns()->detach($campaignId);
        Flux::toast('Campaign restored to your feed');

        $this->clearCaches();
    }

    public function openQuickView(int $campaignId): void
    {
        $this->quickViewCampaignId = $campaignId;
        $this->showQuickViewModal = true;
    }

    public function closeQuickView(): void
    {
        $this->showQuickViewModal = false;
        $this->quickViewCampaignId = null;
    }

    #[On('campaign-applied')]
    public function handleCampaignApplied(): void
    {
        $this->closeQuickView();
    }

    public function getQuickViewCampaignProperty(): ?Campaign
    {
        if (! $this->quickViewCampaignId) {
            return null;
        }

        $campaign = Campaign::with(['business'])->find($this->quickViewCampaignId);

        if ($campaign) {
            $influencerProfile = Auth::user()->influencer;
            if ($influencerProfile) {
                $campaign->match_score = MatchScore::calculateMatchScore($campaign, $influencerProfile, $this->searchRadius);
            }
        }

        return $campaign;
    }

    public function getSavedCampaignIds(): array
    {
        if ($this->cachedSavedCampaignIds === null) {
            $this->cachedSavedCampaignIds = Auth::user()->savedCampaigns()->pluck('campaigns.id')->toArray();
        }

        return $this->cachedSavedCampaignIds;
    }

    public function getHiddenCampaignIds(): array
    {
        if ($this->cachedHiddenCampaignIds === null) {
            $this->cachedHiddenCampaignIds = Auth::user()->hiddenCampaigns()->pluck('campaigns.id')->toArray();
        }

        return $this->cachedHiddenCampaignIds;
    }

    /**
     * Get user's campaign applications keyed by campaign_id (as model instances)
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\CampaignApplication>
     */
    public function getUserApplications(): \Illuminate\Support\Collection
    {
        if ($this->cachedUserApplications === null) {
            $this->cachedUserApplications = \App\Models\CampaignApplication::where('user_id', Auth::id())
                ->get()
                ->keyBy('campaign_id');
        }

        return $this->cachedUserApplications;
    }

    /**
     * Get user's application for a specific campaign
     */
    public function getUserApplicationForCampaign(int $campaignId): ?\App\Models\CampaignApplication
    {
        return $this->getUserApplications()->get($campaignId);
    }

    /**
     * Clear caches when save/hide actions modify the data
     */
    private function clearCaches(): void
    {
        $this->cachedSavedCampaignIds = null;
        $this->cachedHiddenCampaignIds = null;
    }

    public function getDebugData(Campaign $campaign): array
    {
        $user = Auth::user();
        $influencerProfile = $user->influencer;

        $scoreBreakdown = MatchScore::getDetailedScoreBreakdown($campaign, $influencerProfile, $this->searchRadius);

        return [
            'influencer' => [
                'name' => $user->name,
                'email' => $user->email,
                'primary_industry' => $influencerProfile->primary_industry?->value ?? 'Not set',
                'primary_zip_code' => $influencerProfile->postal_code ?? 'Not set',
                'follower_count' => $influencerProfile->follower_count ?? 'Not set',
            ],
            'campaign' => [
                'goal' => $campaign->campaign_goal,
                'description' => $campaign->campaign_description,
                'campaign_type' => $campaign->campaign_type ? $campaign->campaign_type->pluck('value')->join(', ') : 'Not set',
                'compensation_type' => $campaign->compensation_type?->value ?? 'Not set',
                'compensation_amount' => $campaign->compensation_amount ?? 'Not set',
                'target_zip_code' => $campaign->target_zip_code ?? 'Not set',
                'business_industry' => $campaign->business->industry?->value ?? 'Not set',
                'business_name' => $campaign->business->name ?? 'Not set',
                'name' => $campaign->business->name ?? 'Not set',
            ],
            'scores' => $scoreBreakdown,
        ];
    }

    public function render()
    {
        $campaigns = $this->getOpenCampaigns();

        // Create a paginator that works with Livewire
        $currentPage = $this->getPage();
        $offset = ($currentPage - 1) * $this->perPage;
        $paginatedCampaigns = $campaigns->slice($offset, $this->perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedCampaigns,
            $campaigns->count(),
            $this->perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        // Get cached values once
        $savedCampaignIds = $this->getSavedCampaignIds();
        $hiddenCampaignIds = $this->getHiddenCampaignIds();
        $userApplications = $this->getUserApplications();

        return view('livewire.campaigns.influencer-campaigns', [
            'campaigns' => $paginator,
            'nicheOptions' => $this->getNicheOptions(),
            'campaignTypeOptions' => $this->getCampaignTypeOptions(),
            'showDebug' => config('app.debug', false),
            'savedCampaignIds' => $savedCampaignIds,
            'hiddenCampaignIds' => $hiddenCampaignIds,
            'savedCount' => count($savedCampaignIds),
            'hiddenCount' => count($hiddenCampaignIds),
            'userApplications' => $userApplications,
            'quickViewCampaign' => $this->quickViewCampaign,
        ]);
    }
}
