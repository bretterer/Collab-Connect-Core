<?php

namespace App\Livewire;

use App\Enums\AccountType;
use App\Enums\CampaignApplicationStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\PostalCode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard');
    }

    public function getDraftCampaigns()
    {
        if (Auth::user()->account_type === \App\Enums\AccountType::BUSINESS) {
            return Campaign::query()
                ->where('user_id', Auth::user()->id)
                ->where('status', \App\Enums\CampaignStatus::DRAFT)
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        return collect();
    }

    public function getPublishedCampaigns()
    {
        if (Auth::user()->account_type === \App\Enums\AccountType::BUSINESS) {
            return Campaign::query()
                ->where('user_id', Auth::user()->id)
                ->where('status', \App\Enums\CampaignStatus::PUBLISHED)
                ->orderBy('published_at', 'desc')
                ->get();
        }

        return collect();
    }

    public function getScheduledCampaigns()
    {
        if (Auth::user()->account_type === \App\Enums\AccountType::BUSINESS) {
            return Campaign::query()
                ->where('user_id', Auth::user()->id)
                ->where('status', \App\Enums\CampaignStatus::SCHEDULED)
                ->orderBy('scheduled_date', 'asc')
                ->get();
        }

        return collect();
    }

    /**
     * Get recommended campaigns for influencers
     */
    public function getRecommendedCampaigns(): Collection
    {
        if (Auth::user()->account_type !== AccountType::INFLUENCER) {
            return collect();
        }

        $user = Auth::user();
        $influencerProfile = $user->influencerProfile;

        if (! $influencerProfile) {
            return collect();
        }

        // Get published campaigns
        $campaigns = Campaign::query()
            ->where('status', CampaignStatus::PUBLISHED)
            ->where('user_id', '!=', $user->id)
            ->with([
                'user.businessProfile',
                'compensation',
                'requirements',
                'brief',
            ])
            ->get();

        // Calculate match scores and add campaign details
        $scoredCampaigns = $campaigns->map(function ($campaign) use ($influencerProfile) {
            return $this->calculateCampaignMatch($campaign, $influencerProfile);
        })->filter(function ($campaign) {
            return $campaign['match_score'] > 50; // Only show campaigns with 50%+ match
        })->sortByDesc('match_score');

        return $scoredCampaigns->take(3);
    }

    /**
     * Calculate match score between campaign and influencer
     */
    private function calculateCampaignMatch(Campaign $campaign, $influencerProfile): array
    {
        $matchScore = 0;
        $matchReasons = [];

        // Location matching (40% weight)
        $locationScore = $this->calculateLocationMatch($campaign, $influencerProfile);
        $matchScore += $locationScore * 0.4;
        if ($locationScore > 70) {
            $matchReasons[] = 'Local location';
        }

        // Niche matching (30% weight)
        $nicheScore = $this->calculateNicheMatch($campaign, $influencerProfile);
        $matchScore += $nicheScore * 0.3;
        if ($nicheScore > 70) {
            $matchReasons[] = $influencerProfile->primary_niche->label().' niche';
        }

        // Campaign type matching (20% weight)
        $campaignTypeScore = $this->calculateCampaignTypeMatch($campaign, $influencerProfile);
        $matchScore += $campaignTypeScore * 0.2;

        // Compensation matching (10% weight)
        $compensationScore = $this->calculateCompensationMatch($campaign, $influencerProfile);
        $matchScore += $compensationScore * 0.1;
        if ($compensationScore > 70) {
            $matchReasons[] = 'Your follower range';
        }

        // Get distance for display
        $distance = $this->getDistanceBetweenCampaignAndInfluencer($campaign, $influencerProfile);

        return [
            'campaign' => $campaign,
            'match_score' => round($matchScore),
            'match_reasons' => $matchReasons,
            'distance' => $distance,
            'distance_display' => $distance ? round($distance).' miles away' : 'Distance unknown',
            'posted_ago' => $campaign->published_at ? $campaign->published_at->diffForHumans() : 'Recently posted',
        ];
    }

    /**
     * Calculate location match score (0-100)
     */
    private function calculateLocationMatch(Campaign $campaign, $influencerProfile): int
    {
        if (! $campaign->target_zip_code || ! $influencerProfile->primary_zip_code) {
            return 20; // Default low score if no location data
        }

        $distance = $this->getDistanceBetweenCampaignAndInfluencer($campaign, $influencerProfile);

        if ($distance === null) {
            return 20;
        }

        // Perfect match within 5 miles, declining score with distance
        if ($distance <= 5) {
            return 100;
        }
        if ($distance <= 10) {
            return 90;
        }
        if ($distance <= 25) {
            return 80;
        }
        if ($distance <= 50) {
            return 70;
        }
        if ($distance <= 100) {
            return 50;
        }

        return 20;
    }

    /**
     * Calculate niche match score (0-100)
     */
    private function calculateNicheMatch(Campaign $campaign, $influencerProfile): int
    {
        if (! $influencerProfile->primary_niche) {
            return 30;
        }

        // For now, we'll use some basic niche matching logic
        // This could be expanded to include campaign requirements or business industry
        $influencerNiche = $influencerProfile->primary_niche;
        $businessProfile = $campaign->user->businessProfile;

        if (! $businessProfile || ! $businessProfile->industry) {
            return 50; // Neutral score if no business industry data
        }

        // Direct match logic (this could be more sophisticated)
        $nicheMatchMap = [
            'FOOD' => ['RESTAURANT', 'FOOD_BEVERAGE'],
            'FITNESS' => ['FITNESS', 'HEALTH_WELLNESS'],
            'FASHION' => ['FASHION', 'RETAIL'],
            'LIFESTYLE' => ['LIFESTYLE', 'RETAIL', 'RESTAURANT'],
            'BEAUTY' => ['BEAUTY', 'HEALTH_WELLNESS'],
            'TRAVEL' => ['TRAVEL', 'HOSPITALITY'],
            'TECH' => ['TECHNOLOGY', 'BUSINESS'],
        ];

        $matchingIndustries = $nicheMatchMap[$influencerNiche->value] ?? [];

        if (in_array($businessProfile->industry?->value, $matchingIndustries)) {
            return 95;
        }

        return 40; // Default moderate score
    }

    /**
     * Calculate campaign type match score (0-100)
     */
    private function calculateCampaignTypeMatch(Campaign $campaign, $influencerProfile): int
    {
        // This could be expanded based on influencer preferences
        // For now, return a neutral score
        return 70;
    }

    /**
     * Calculate compensation match score (0-100)
     */
    private function calculateCompensationMatch(Campaign $campaign, $influencerProfile): int
    {
        // This could factor in influencer's typical rates, follower count, etc.
        // For now, return a neutral score
        return 75;
    }

    /**
     * Get distance between campaign and influencer
     */
    private function getDistanceBetweenCampaignAndInfluencer(Campaign $campaign, $influencerProfile): ?float
    {
        if (! $campaign->target_zip_code || ! $influencerProfile->primary_zip_code) {
            return null;
        }

        $campaignPostal = PostalCode::where('postal_code', $campaign->target_zip_code)
            ->where('country_code', 'US')
            ->first();

        $influencerPostal = PostalCode::where('postal_code', $influencerProfile->primary_zip_code)
            ->where('country_code', 'US')
            ->first();

        if (! $campaignPostal || ! $influencerPostal) {
            return null;
        }

        return $campaignPostal->distanceTo($influencerPostal);
    }

    /**
     * Get campaigns the influencer has applied to
     */
    public function getInfluencerApplications(): Collection
    {
        if (Auth::user()->account_type !== AccountType::INFLUENCER) {
            return collect();
        }

        return \App\Models\CampaignApplication::query()
            ->where('user_id', Auth::user()->id)
            ->with(['campaign.user.businessProfile', 'campaign.compensation'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get active campaigns the influencer is participating in
     */
    public function getActiveCampaigns(): Collection
    {
        if (Auth::user()->account_type !== AccountType::INFLUENCER) {
            return collect();
        }

        return \App\Models\CampaignApplication::query()
            ->where('user_id', Auth::user()->id)
            ->where('status', CampaignApplicationStatus::ACCEPTED)
            ->with(['campaign.user.businessProfile', 'campaign.compensation'])
            ->whereHas('campaign', function ($query) {
                $query->where('status', \App\Enums\CampaignStatus::PUBLISHED);
            })
            ->orderBy('accepted_at', 'desc')
            ->get();
    }

    /**
     * Get pending applications for business users
     */
    public function getPendingApplications(): Collection
    {
        if (Auth::user()->account_type !== AccountType::BUSINESS) {
            return collect();
        }

        return \App\Models\CampaignApplication::query()
            ->where('status', CampaignApplicationStatus::PENDING)
            ->whereHas('campaign', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })
            ->with([
                'user.influencerProfile',
                'campaign',
                'user.socialMediaAccounts',
            ])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get total application count for business stats
     */
    public function getTotalApplicationsCount(): int
    {
        if (Auth::user()->account_type !== AccountType::BUSINESS) {
            return 0;
        }

        return \App\Models\CampaignApplication::query()
            ->whereHas('campaign', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })
            ->count();
    }

    /**
     * Accept an application
     */
    public function acceptApplication($applicationId)
    {
        $application = \App\Models\CampaignApplication::find($applicationId);

        if (! $application || ! $application->campaign || $application->campaign->user_id !== Auth::user()->id) {
            $this->flashError('Application not found or you do not have permission to accept it.');

            return;
        }

        $application->update([
            'status' => CampaignApplicationStatus::ACCEPTED,
            'accepted_at' => now(),
        ]);

        $this->flashSuccess('Application accepted successfully!');
    }

    /**
     * Decline an application
     */
    public function declineApplication($applicationId)
    {
        $application = \App\Models\CampaignApplication::find($applicationId);

        if (! $application || ! $application->campaign || $application->campaign->user_id !== Auth::user()->id) {
            $this->flashError('Application not found or you do not have permission to decline it.');

            return;
        }

        $application->update([
            'status' => CampaignApplicationStatus::REJECTED,
            'rejected_at' => now(),
        ]);

        $this->flashSuccess('Application declined.');
    }
}
