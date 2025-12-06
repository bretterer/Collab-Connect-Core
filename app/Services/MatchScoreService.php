<?php

namespace App\Services;

use App\Enums\BusinessIndustry;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Models\Campaign;
use App\Models\PostalCode;

class MatchScoreService
{
    public function calculateMatchScore(Campaign $campaign, $influencerProfile, int $searchRadius = 50): float
    {
        $score = 0.0;
        $maxScore = 100.0;

        $score += $this->calculateLocationScore($campaign, $influencerProfile, $searchRadius) * 0.35;
        $score += $this->calculateIndustryScore($campaign, $influencerProfile) * 0.35;
        $score += $this->calculateCampaignTypeScore($campaign, $influencerProfile) * 0.2;
        $score += $this->calculateCompensationScore($campaign, $influencerProfile) * 0.1;

        return min($score, $maxScore);
    }

    public function calculateLocationScore(Campaign $campaign, $influencerProfile, int $searchRadius): float
    {
        $campaignZipCode = $campaign->target_zip_code;
        $influencerZipCode = $influencerProfile->postal_code;

        if (! $campaignZipCode || ! $influencerZipCode) {
            return 50.0;
        }

        if ($campaignZipCode === $influencerZipCode) {
            return 100.0;
        }

        $campaignPostalCode = PostalCode::where('postal_code', $campaignZipCode)->first();
        $influencerPostalCode = PostalCode::where('postal_code', $influencerZipCode)->first();

        if ($campaignPostalCode && $influencerPostalCode) {
            $distance = $campaignPostalCode->distanceTo($influencerPostalCode);

            if ($distance <= $searchRadius) {
                return max(60.0, 100.0 - ($distance / $searchRadius) * 40.0);
            }
        }

        $baseScore = 25.0;
        $business = $campaign->business;
        $variation = 0;

        if ($business) {
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

        $randomFactor = (ord(substr($campaign->campaign_goal, 0, 1)) % 20) - 10;
        $finalScore = max(15.0, min(50.0, $baseScore + $variation + $randomFactor));

        return $finalScore;
    }

    public function calculateIndustryScore(Campaign $campaign, $influencerProfile): float
    {
        $influencerIndustry = $influencerProfile->primary_industry;

        if (! $influencerIndustry) {
            return 50.0;
        }

        $business = $campaign->business;
        if (! $business) {
            return 50.0;
        }

        $businessIndustry = $business->industry;

        if ($influencerIndustry === $businessIndustry) {
            return 100.0;
        }

        $relatedIndustries = [
            BusinessIndustry::FOOD_BEVERAGE->value => [BusinessIndustry::FITNESS_WELLNESS, BusinessIndustry::TRAVEL_TOURISM],
            BusinessIndustry::FASHION_APPAREL->value => [BusinessIndustry::BEAUTY_COSMETICS, BusinessIndustry::FITNESS_WELLNESS],
            BusinessIndustry::BEAUTY_COSMETICS->value => [BusinessIndustry::FASHION_APPAREL, BusinessIndustry::FITNESS_WELLNESS],
            BusinessIndustry::FITNESS_WELLNESS->value => [BusinessIndustry::HEALTHCARE, BusinessIndustry::FOOD_BEVERAGE],
            BusinessIndustry::HOME_GARDEN->value => [BusinessIndustry::RETAIL, BusinessIndustry::BABY_KIDS],
            BusinessIndustry::TRAVEL_TOURISM->value => [BusinessIndustry::FOOD_BEVERAGE, BusinessIndustry::ENTERTAINMENT],
            BusinessIndustry::RETAIL->value => [BusinessIndustry::FASHION_APPAREL, BusinessIndustry::HOME_GARDEN],
        ];

        $influencerIndustryValue = is_object($influencerIndustry) ? $influencerIndustry->value : $influencerIndustry;

        if (isset($relatedIndustries[$influencerIndustryValue]) && in_array($businessIndustry, $relatedIndustries[$influencerIndustryValue])) {
            return 80.0;
        }

        $baseScore = 35.0;
        $campaignText = strtolower($campaign->campaign_goal.' '.$campaign->campaign_description);
        $variation = 0;

        $keywords = ['fashion', 'beauty', 'fitness', 'food', 'travel', 'lifestyle', 'home', 'family'];
        foreach ($keywords as $keyword) {
            if (str_contains($campaignText, $keyword)) {
                $variation += 8;
            }
        }

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

    public function calculateCampaignTypeScore(Campaign $campaign, $influencerProfile): float
    {
        $baseScore = 70.0;
        $campaignTypes = $campaign->campaign_type ?: collect();
        $maxVariation = 0;

        // Calculate the best variation from all campaign types
        foreach ($campaignTypes as $typeEnum) {
            $variation = 0;

            switch ($typeEnum) {
                case CampaignType::PRODUCT_REVIEWS:
                    $variation = 10;
                    break;
                case CampaignType::SPONSORED_POSTS:
                    $variation = 5;
                    break;
                case CampaignType::EVENT_COVERAGE:
                    $variation = -5;
                    break;
                case CampaignType::BRAND_PARTNERSHIPS:
                    $variation = 15;
                    break;
                default:
                    $variation = 0;
            }

            $maxVariation = max($maxVariation, $variation);
        }

        $randomFactor = (ord(substr($campaign->campaign_goal, -1)) % 20) - 10;
        $finalScore = max(50.0, min(90.0, $baseScore + $maxVariation + $randomFactor));

        return $finalScore;
    }

    public function calculateCompensationScore(Campaign $campaign, $influencerProfile): float
    {
        $baseScore = 70.0;
        $compensationType = $campaign->compensation_type;
        $variation = 0;

        switch ($compensationType) {
            case CompensationType::MONETARY:
                $variation = 10;
                break;
            case CompensationType::FREE_PRODUCT:
                $variation = 5;
                break;
            case CompensationType::EXPERIENCE:
                $variation = -5;
                break;
            case CompensationType::GIFT_CARD:
                $variation = 8;
                break;
            case CompensationType::DISCOUNT:
                $variation = 2;
                break;
            default:
                $variation = 0;
        }

        if ($campaign->compensation_amount) {
            $amountFactor = min(20, $campaign->compensation_amount / 100);
            $variation += $amountFactor;
        }

        $randomFactor = (ord(substr($campaign->campaign_goal, 1, 1)) % 15) - 7;
        $finalScore = max(50.0, min(90.0, $baseScore + $variation + $randomFactor));

        return $finalScore;
    }

    public function getDetailedScoreBreakdown(Campaign $campaign, $influencerProfile, int $searchRadius = 50): array
    {
        $locationScore = $this->calculateLocationScore($campaign, $influencerProfile, $searchRadius);
        $industryScore = $this->calculateIndustryScore($campaign, $influencerProfile);
        $campaignTypeScore = $this->calculateCampaignTypeScore($campaign, $influencerProfile);
        $compensationScore = $this->calculateCompensationScore($campaign, $influencerProfile);

        $locationWeighted = $locationScore * 0.35;
        $industryWeighted = $industryScore * 0.35;
        $campaignTypeWeighted = $campaignTypeScore * 0.2;
        $compensationWeighted = $compensationScore * 0.1;

        $totalScore = $locationWeighted + $industryWeighted + $campaignTypeWeighted + $compensationWeighted;

        return [
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
            'total' => round($totalScore, 1),
        ];
    }
}
