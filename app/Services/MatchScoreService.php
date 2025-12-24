<?php

namespace App\Services;

use App\Enums\BusinessIndustry;
use App\Models\Campaign;
use App\Models\PostalCode;
use Illuminate\Support\Collection;

class MatchScoreService
{
    /**
     * Cache of postal codes keyed by postal_code string
     */
    private array $postalCodeCache = [];

    /**
     * Pre-load postal codes for a batch of campaigns and an influencer.
     * This reduces N+1 queries when calculating scores for multiple campaigns.
     *
     * @param  Collection<Campaign>  $campaigns
     */
    public function preloadPostalCodes(Collection $campaigns, $influencerProfile): void
    {
        $postalCodes = collect();

        // Collect all unique postal codes we need
        $campaignPostalCodes = $campaigns->pluck('target_zip_code')->filter()->unique();
        $postalCodes = $postalCodes->merge($campaignPostalCodes);

        if ($influencerProfile->postal_code) {
            $postalCodes->push($influencerProfile->postal_code);
        }

        $postalCodes = $postalCodes->unique()->values();

        if ($postalCodes->isEmpty()) {
            return;
        }

        // Single query to load all postal codes
        $loaded = PostalCode::whereIn('postal_code', $postalCodes)->get();

        // Cache them by postal_code
        foreach ($loaded as $postalCode) {
            $this->postalCodeCache[$postalCode->postal_code] = $postalCode;
        }
    }

    /**
     * Get a postal code from cache or database
     */
    private function getPostalCode(?string $postalCode): ?PostalCode
    {
        if (! $postalCode) {
            return null;
        }

        if (isset($this->postalCodeCache[$postalCode])) {
            return $this->postalCodeCache[$postalCode];
        }

        // Fallback to database if not cached (shouldn't happen if preload was called)
        $loaded = PostalCode::where('postal_code', $postalCode)->first();
        if ($loaded) {
            $this->postalCodeCache[$postalCode] = $loaded;
        }

        return $loaded;
    }

    /**
     * Pre-load postal codes for one campaign and multiple influencer profiles.
     * This reduces N+1 queries when calculating scores for multiple influencers.
     */
    public function preloadPostalCodesForInfluencers(Campaign $campaign, Collection $influencerProfiles): void
    {
        $postalCodes = collect();

        // Add campaign postal code
        if ($campaign->target_zip_code) {
            $postalCodes->push($campaign->target_zip_code);
        }

        // Collect all unique influencer postal codes
        $influencerPostalCodes = $influencerProfiles->pluck('postal_code')->filter()->unique();
        $postalCodes = $postalCodes->merge($influencerPostalCodes);

        $postalCodes = $postalCodes->unique()->values();

        if ($postalCodes->isEmpty()) {
            return;
        }

        // Single query to load all postal codes
        $loaded = PostalCode::whereIn('postal_code', $postalCodes)->get();

        // Cache them by postal_code
        foreach ($loaded as $postalCode) {
            $this->postalCodeCache[$postalCode->postal_code] = $postalCode;
        }
    }

    /**
     * Clear the postal code cache
     */
    public function clearCache(): void
    {
        $this->postalCodeCache = [];
    }

    /**
     * The boost bonus percentage for boosted campaigns.
     */
    public const BOOST_BONUS = 5.0;

    public function calculateMatchScore(Campaign $campaign, $influencerProfile, int $searchRadius = 50): float
    {
        $score = 0.0;
        $maxScore = 100.0;

        $score += $this->calculateLocationScore($campaign, $influencerProfile, $searchRadius) * 0.35;
        $score += $this->calculateIndustryScore($campaign, $influencerProfile) * 0.35;
        $score += $this->calculateCampaignTypeScore($campaign, $influencerProfile) * 0.2;
        $score += $this->calculateCompensationScore($campaign, $influencerProfile) * 0.1;

        $finalScore = min($score, $maxScore);

        // Add boost bonus if campaign is currently boosted
        if ($campaign->isBoosted()) {
            $finalScore = min($finalScore + self::BOOST_BONUS, $maxScore);
        }

        return $finalScore;
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

        $campaignPostalCode = $this->getPostalCode($campaignZipCode);
        $influencerPostalCode = $this->getPostalCode($influencerZipCode);

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
        $campaignTypes = $campaign->campaign_type ?: collect();
        $influencerContentTypes = $influencerProfile->content_types ?? [];

        // If influencer has no content type preferences, return neutral score
        if (empty($influencerContentTypes)) {
            return 50.0;
        }

        // If campaign has no types set, return neutral score
        if ($campaignTypes->isEmpty()) {
            return 50.0;
        }

        // Convert campaign types to values for comparison
        $campaignTypeValues = $campaignTypes->map(fn ($type) => $type->value)->toArray();

        // Check for exact matches between campaign types and influencer's preferred content types
        $matches = array_intersect($campaignTypeValues, $influencerContentTypes);

        if (count($matches) > 0) {
            // At least one match - score based on how many matches
            $matchRatio = count($matches) / count($campaignTypeValues);

            return 70.0 + ($matchRatio * 30.0); // 70-100 based on match ratio
        }

        // No direct matches - return lower score
        return 40.0;
    }

    public function calculateCompensationScore(Campaign $campaign, $influencerProfile): float
    {
        $campaignCompensationType = $campaign->compensation_type;
        $influencerCompensationTypes = $influencerProfile->compensation_types ?? [];

        // If influencer has no compensation preferences, return neutral score
        if (empty($influencerCompensationTypes)) {
            return 50.0;
        }

        // If campaign has no compensation type, return neutral score
        if (! $campaignCompensationType) {
            return 50.0;
        }

        $campaignCompensationValue = $campaignCompensationType->value ?? $campaignCompensationType;

        // Check if campaign's compensation type matches influencer's preferences
        if (in_array($campaignCompensationValue, $influencerCompensationTypes)) {
            return 100.0;
        }

        // No match - return lower score
        return 40.0;
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
