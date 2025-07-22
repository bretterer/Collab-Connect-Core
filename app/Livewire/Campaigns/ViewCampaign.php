<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use App\Models\User;
use App\Livewire\BaseComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ViewCampaign extends BaseComponent
{
    public Campaign $campaign;
    public User $campaignOwner;
    public float $matchScore;

    public function mount($campaign)
    {
        // Load the campaign with relationships
        $this->campaign = Campaign::with(['user.businessProfile'])
            ->where('id', $campaign)
            ->where('status', \App\Enums\CampaignStatus::PUBLISHED)
            ->firstOrFail();

        $this->campaignOwner = $this->campaign->user;

        // Calculate match score for the current influencer
        $influencerProfile = Auth::user()->influencerProfile;
        if ($influencerProfile) {
            $this->matchScore = $this->calculateMatchScore($this->campaign, $influencerProfile);
        } else {
            $this->matchScore = 0;
        }
    }

    public function render()
    {
        return view('livewire.campaigns.view-campaign');
    }

    public function applyToCampaign()
    {
        // TODO: Implement application logic
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Application submitted successfully!'
        ]);
    }

    public function backToDiscover()
    {
        return redirect()->route('discover');
    }

    private function calculateMatchScore(Campaign $campaign, $influencerProfile): float
    {
        $score = 0.0;
        $maxScore = 100.0;

        // Location match (35% weight)
        $locationScore = $this->calculateLocationScore($campaign, $influencerProfile);
        $score += $locationScore * 0.35;

        // Niche match (35% weight)
        $nicheScore = $this->calculateNicheScore($campaign, $influencerProfile);
        $score += $nicheScore * 0.35;

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

        if (!$campaignZipCode || !$influencerZipCode) {
            return 50.0; // Neutral score if location data is missing
        }

        // Exact match
        if ($campaignZipCode === $influencerZipCode) {
            return 100.0;
        }

        // Calculate distance (simplified)
        $distance = abs((int)$campaignZipCode - (int)$influencerZipCode);

        if ($distance <= 1000) {
            return 90.0;
        } elseif ($distance <= 5000) {
            return 75.0;
        } elseif ($distance <= 10000) {
            return 60.0;
        } else {
            return 30.0;
        }
    }

    private function calculateNicheScore(Campaign $campaign, $influencerProfile): float
    {
        $campaignIndustry = $campaign->user->businessProfile?->industry;
        $influencerNiche = $influencerProfile->primary_niche;

        if (!$campaignIndustry || !$influencerNiche) {
            return 50.0;
        }

        // Exact match
        if ($campaignIndustry === $influencerNiche) {
            return 100.0;
        }

        // Industry appeal variations
        $industryAppeal = [
            'fashion' => ['beauty', 'lifestyle'],
            'beauty' => ['fashion', 'lifestyle'],
            'fitness' => ['health', 'lifestyle'],
            'food' => ['lifestyle', 'travel'],
            'travel' => ['lifestyle', 'food'],
            'tech' => ['business', 'education'],
            'business' => ['tech', 'education'],
            'education' => ['tech', 'business'],
            'lifestyle' => ['fashion', 'beauty', 'food', 'travel'],
        ];

        $appeal = $industryAppeal[$campaignIndustry->value] ?? [];
        if (in_array($influencerNiche->value, $appeal)) {
            return 80.0;
        }

        return 40.0;
    }

    private function calculateCampaignTypeScore(Campaign $campaign, $influencerProfile): float
    {
        $campaignType = $campaign->campaign_type;

        // Base score for campaign type preference
        $typeScores = [
            'sponsored_posts' => 85.0,
            'product_reviews' => 80.0,
            'brand_ambassador' => 90.0,
            'event_promotion' => 75.0,
            'giveaway' => 70.0,
        ];

        return $typeScores[$campaignType->value] ?? 70.0;
    }

    private function calculateCompensationScore(Campaign $campaign, $influencerProfile): float
    {
        $compensationType = $campaign->compensation_type;
        $compensationAmount = $campaign->compensation_amount;
        $influencerFollowers = $influencerProfile->follower_count ?? 0;

        // Base score for compensation type
        $typeScores = [
            'monetary' => 90.0,
            'product' => 70.0,
            'commission' => 85.0,
            'exposure' => 60.0,
        ];

        $baseScore = $typeScores[$compensationType->value] ?? 70.0;

        // Adjust based on compensation amount and follower count
        if ($compensationType->value === 'monetary' && $compensationAmount) {
            if ($influencerFollowers > 100000 && $compensationAmount >= 500) {
                $baseScore += 10.0;
            } elseif ($influencerFollowers > 10000 && $compensationAmount >= 100) {
                $baseScore += 5.0;
            }
        }

        return min($baseScore, 100.0);
    }
}