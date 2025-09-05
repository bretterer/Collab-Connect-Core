<?php

namespace App\Livewire\Campaigns;

use App\Enums\AccountType;
use App\Enums\BusinessIndustry;
use App\Enums\CampaignStatus;
use App\Facades\MatchScore;
use App\Livewire\BaseComponent;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
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


    protected $queryString = [
        'search' => ['except' => ''],
        'selectedNiches' => ['except' => []],
        'selectedCampaignTypes' => ['except' => []],
        'sortBy' => ['except' => 'match_score'],
        'perPage' => ['except' => 12],
    ];

    public function mount()
    {
        // redirect business users to their campaigns
        if (Auth::user()->account_type === AccountType::BUSINESS) {
            return redirect()->route('campaigns.index');
        }
        // URL parameters will be automatically loaded due to $queryString property
    }

    public function getOpenCampaigns()
    {
        $user = Auth::user();
        $influencerProfile = $user->influencer;

        if (! $influencerProfile) {
            return collect();
        }

        $query = Campaign::query()
            ->where('status', CampaignStatus::PUBLISHED)
            ->where('business_id', '!=', $user->current_business) // Exclude own business campaigns
            ->where('application_deadline', '>', now())
            ->with(['business']);

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('campaign_goal', 'like', '%'.$this->search.'%')
                    ->orWhere('campaign_description', 'like', '%'.$this->search.'%');
            });
        }

        // Apply niche filter
        if (! empty($this->selectedNiches)) {
            $query->whereHas('business', function ($q) {
                $q->whereIn('industry', $this->selectedNiches);
            });
        }

        // Apply campaign type filter
        if (! empty($this->selectedCampaignTypes)) {
            $query->whereIn('campaign_type', $this->selectedCampaignTypes);
        }

        $campaigns = $query->get();

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

        return view('livewire.campaigns.influencer-campaigns', [
            'campaigns' => $paginator,
            'nicheOptions' => $this->getNicheOptions(),
            'campaignTypeOptions' => $this->getCampaignTypeOptions(),
            'showDebug' => config('app.debug', false),
        ]);
    }
}
