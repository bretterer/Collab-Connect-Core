<?php

namespace App\Facades;

use App\Services\MatchScoreService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void preloadPostalCodes(\Illuminate\Support\Collection $campaigns, $influencerProfile)
 * @method static void preloadPostalCodesForInfluencers(\App\Models\Campaign $campaign, \Illuminate\Support\Collection $influencerProfiles)
 * @method static void clearCache()
 * @method static float calculateMatchScore(\App\Models\Campaign $campaign, $influencerProfile, int $searchRadius = 50)
 * @method static float calculateLocationScore(\App\Models\Campaign $campaign, $influencerProfile, int $searchRadius)
 * @method static float calculateIndustryScore(\App\Models\Campaign $campaign, $influencerProfile)
 * @method static float calculateCampaignTypeScore(\App\Models\Campaign $campaign, $influencerProfile)
 * @method static float calculateCompensationScore(\App\Models\Campaign $campaign, $influencerProfile)
 * @method static array getDetailedScoreBreakdown(\App\Models\Campaign $campaign, $influencerProfile, int $searchRadius = 50)
 *
 * @see \App\Services\MatchScoreService
 */
class MatchScore extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MatchScoreService::class;
    }
}
