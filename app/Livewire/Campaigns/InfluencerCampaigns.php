<?php

namespace App\Livewire\Campaigns;

use App\Enums\AccountType;
use App\Enums\BusinessIndustry;
use App\Enums\CampaignStatus;
use App\Livewire\BaseComponent;
use App\Models\Campaign;
use App\Models\PostalCode;
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
            $campaign->match_score = $this->calculateMatchScore($campaign, $influencerProfile);

            return $campaign;
        });

        // Sort by match score or other criteria
        $campaigns = $this->sortCampaigns($campaigns);

        // Return the collection for Livewire pagination
        return $campaigns;
    }

    private function calculateMatchScore(Campaign $campaign, $influencerProfile): float
    {
        $score = 0.0;
        $maxScore = 100.0;

        // Location match (35% weight)
        $locationScore = $this->calculateLocationScore($campaign, $influencerProfile);
        $score += $locationScore * 0.35;

        // Industry match (35% weight)
        $industryScore = $this->calculateIndustryScore($campaign, $influencerProfile);
        $score += $industryScore * 0.35;

        // Campaign type match (20% weight)
        $campaignTypeScore = $this->calculateCampaignTypeScore($campaign, $influencerProfile);
        $score += $campaignTypeScore * 0.2;

        // Compensation match (10% weight)
        $compensationScore = $this->calculateCompensationScore($campaign, $influencerProfile);
        $score += $compensationScore * 0.1;

        return min($score, $maxScore);
    }

    private function calculateLocationScore(Campaign $campaign, $influencerProfile): float
    {
        $campaignZipCode = $campaign->target_zip_code;
        $influencerZipCode = $influencerProfile->primary_zip_code;

        if (! $campaignZipCode || ! $influencerZipCode) {
            return 50.0; // Neutral score if location data is missing
        }

        // Exact match
        if ($campaignZipCode === $influencerZipCode) {
            return 100.0;
        }

        // Check if within search radius
        $campaignPostalCode = PostalCode::where('postal_code', $campaignZipCode)->first();
        $influencerPostalCode = PostalCode::where('postal_code', $influencerZipCode)->first();

        if ($campaignPostalCode && $influencerPostalCode) {
            $distance = $campaignPostalCode->distanceTo($influencerPostalCode);

            if ($distance <= $this->searchRadius) {
                // Score based on distance (closer = higher score)
                return max(60.0, 100.0 - ($distance / $this->searchRadius) * 40.0);
            }
        }

        // More varied scoring for distant locations based on campaign data
        $baseScore = 25.0;

        // Add variation based on campaign characteristics and business industry
        $business = $campaign->business;
        $variation = 0;

        if ($business) {
            // Different industries might have different appeal for remote work
            $industryVariations = [
                'fashion' => 5,
                'beauty' => 3,
                'fitness' => 8,
                'food' => -2,
                'travel' => 10,
                'lifestyle' => 7,
                'home' => 2,
                'family' => 1,
            ];

            $industry = $business->industry;
            if (is_object($industry)) {
                $industry = $industry->value;
            }

            $variation += $industryVariations[$industry] ?? 0;
        }

        // Add some randomness based on campaign characteristics
        $randomFactor = (ord(substr($campaign->campaign_goal, 0, 1)) % 20) - 10; // -10 to +10
        $finalScore = max(15.0, min(50.0, $baseScore + $variation + $randomFactor));

        return $finalScore;
    }

    private function calculateIndustryScore(Campaign $campaign, $influencerProfile): float
    {
        $influencerIndustry = $influencerProfile->primary_industry;

        if (! $influencerIndustry) {
            return 50.0;
        }

        // Get business industry from campaign
        $business = $campaign->business;
        if (! $business) {
            return 50.0;
        }

        $businessIndustry = $business->industry;

        // Exact industry match
        if ($influencerIndustry === $businessIndustry) {
            return 100.0;
        }

        // Related industries (you can expand this mapping)
        $relatedIndustries = [
            BusinessIndustry::FOOD_BEVERAGE => [BusinessIndustry::FITNESS_WELLNESS, BusinessIndustry::TRAVEL_TOURISM],
            BusinessIndustry::FASHION_APPAREL => [BusinessIndustry::BEAUTY_COSMETICS, BusinessIndustry::FITNESS_WELLNESS],
            BusinessIndustry::BEAUTY_COSMETICS => [BusinessIndustry::FASHION_APPAREL, BusinessIndustry::FITNESS_WELLNESS],
            BusinessIndustry::FITNESS_WELLNESS => [BusinessIndustry::HEALTHCARE, BusinessIndustry::FOOD_BEVERAGE],
            BusinessIndustry::HOME_GARDEN => [BusinessIndustry::RETAIL, BusinessIndustry::BABY_KIDS],
            BusinessIndustry::TRAVEL_TOURISM => [BusinessIndustry::FOOD_BEVERAGE, BusinessIndustry::ENTERTAINMENT],
            BusinessIndustry::RETAIL => [BusinessIndustry::FASHION_APPAREL, BusinessIndustry::HOME_GARDEN],
        ];

        if (isset($relatedIndustries[$influencerIndustry]) && in_array($businessIndustry, $relatedIndustries[$influencerIndustry])) {
            return 80.0;
        }

        // More varied scoring for unrelated niches
        $baseScore = 35.0;

        // Add variation based on campaign content analysis
        $campaignText = strtolower($campaign->campaign_goal.' '.$campaign->campaign_description);
        $variation = 0;

        // Check for keywords that might indicate some relevance
        $keywords = ['fashion', 'beauty', 'fitness', 'food', 'travel', 'lifestyle', 'home', 'family'];
        foreach ($keywords as $keyword) {
            if (str_contains($campaignText, $keyword)) {
                $variation += 8;
            }
        }

        // Add variation based on business industry appeal
        $business = $campaign->business;
        if ($business) {
            $industryAppeal = [
                'fashion' => 10,
                'beauty' => 8,
                'fitness' => 12,
                'food' => 5,
                'travel' => 15,
                'lifestyle' => 10,
                'home' => 6,
                'family' => 4,
            ];

            $industry = $business->industry;
            if (is_object($industry)) {
                $industry = $industry->value;
            }

            $variation += $industryAppeal[$industry] ?? 0;
        }

        $finalScore = max(25.0, min(70.0, $baseScore + $variation));

        return $finalScore;
    }

    private function calculateCampaignTypeScore(Campaign $campaign, $influencerProfile): float
    {
        // More varied scoring based on campaign type and characteristics
        $baseScore = 70.0;

        // Add variation based on campaign type
        $campaignType = $campaign->campaign_type;
        $variation = 0;

        // Different campaign types might appeal differently
        switch ($campaignType) {
            case \App\Enums\CampaignType::PRODUCT_REVIEWS:
                $variation = 10; // Generally popular
                break;
            case \App\Enums\CampaignType::SPONSORED_POSTS:
                $variation = 5; // Standard
                break;
            case \App\Enums\CampaignType::EVENT_COVERAGE:
                $variation = -5; // More specific
                break;
            case \App\Enums\CampaignType::BRAND_PARTNERSHIPS:
                $variation = 15; // High value
                break;
            default:
                $variation = 0;
        }

        // Add some randomness based on campaign characteristics
        $randomFactor = (ord(substr($campaign->campaign_goal, -1)) % 20) - 10; // -10 to +10

        $finalScore = max(50.0, min(90.0, $baseScore + $variation + $randomFactor));

        return $finalScore;
    }

    private function calculateCompensationScore(Campaign $campaign, $influencerProfile): float
    {
        // More varied scoring based on compensation type and amount
        $baseScore = 70.0;

        // Add variation based on compensation type
        $compensationType = $campaign->compensation_type;
        $variation = 0;

        switch ($compensationType) {
            case \App\Enums\CompensationType::MONETARY:
                $variation = 10; // Generally preferred
                break;
            case \App\Enums\CompensationType::FREE_PRODUCT:
                $variation = 5; // Good for some influencers
                break;
            case \App\Enums\CompensationType::EXPERIENCE:
                $variation = -5; // More specific appeal
                break;
            case \App\Enums\CompensationType::GIFT_CARD:
                $variation = 8; // Good value
                break;
            case \App\Enums\CompensationType::BARTER:
                $variation = 3; // Moderate appeal
                break;
            case \App\Enums\CompensationType::DISCOUNT:
                $variation = 2; // Lower appeal
                break;
            default:
                $variation = 0;
        }

        // Add variation based on compensation amount (if available)
        if ($campaign->compensation_amount) {
            $amountFactor = min(20, $campaign->compensation_amount / 100); // Cap at 20 points
            $variation += $amountFactor;
        }

        // Add some randomness based on campaign characteristics
        $randomFactor = (ord(substr($campaign->campaign_goal, 1, 1)) % 15) - 7; // -7 to +7

        $finalScore = max(50.0, min(90.0, $baseScore + $variation + $randomFactor));

        return $finalScore;
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

        // Calculate individual scores
        $locationScore = $this->calculateLocationScore($campaign, $influencerProfile);
        $industryScore = $this->calculateIndustryScore($campaign, $influencerProfile);
        $campaignTypeScore = $this->calculateCampaignTypeScore($campaign, $influencerProfile);
        $compensationScore = $this->calculateCompensationScore($campaign, $influencerProfile);

        // Calculate weighted scores
        $locationWeighted = $locationScore * 0.35;
        $industryWeighted = $industryScore * 0.35;
        $campaignTypeWeighted = $campaignTypeScore * 0.2;
        $compensationWeighted = $compensationScore * 0.1;

        return [
            'influencer' => [
                'name' => $user->name,
                'email' => $user->email,
                'primary_industry' => $influencerProfile->primary_industry?->value ?? 'Not set',
                'primary_zip_code' => $influencerProfile->primary_zip_code ?? 'Not set',
                'follower_count' => $influencerProfile->follower_count ?? 'Not set',
            ],
            'campaign' => [
                'goal' => $campaign->campaign_goal,
                'description' => $campaign->campaign_description,
                'campaign_type' => $campaign->campaign_type?->value ?? 'Not set',
                'compensation_type' => $campaign->compensation_type?->value ?? 'Not set',
                'compensation_amount' => $campaign->compensation_amount ?? 'Not set',
                'target_zip_code' => $campaign->target_zip_code ?? 'Not set',
                'business_industry' => $campaign->business->industry?->value ?? 'Not set',
                'business_name' => $campaign->business->name ?? 'Not set',
                'name' => $campaign->business->name ?? 'Not set',
            ],
            'scores' => [
                'location' => [
                    'raw' => round($locationScore, 1),
                    'weighted' => round($locationWeighted, 1),
                    'weight' => '35%',
                ],
                'industry' => [
                    'raw' => round($industryScore, 1),
                    'weighted' => round($industryWeighted, 1),
                    'weight' => '35%',
                ],
                'campaign_type' => [
                    'raw' => round($campaignTypeScore, 1),
                    'weighted' => round($campaignTypeWeighted, 1),
                    'weight' => '20%',
                ],
                'compensation' => [
                    'raw' => round($compensationScore, 1),
                    'weighted' => round($compensationWeighted, 1),
                    'weight' => '10%',
                ],
                'total' => round($campaign->match_score, 1),
            ],
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
