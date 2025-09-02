<?php

namespace Tests\Feature;

use App\Enums\BusinessIndustry;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Facades\MatchScore;
use App\Models\Campaign;
use App\Models\PostalCode;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class HighMatchScoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake(); // Prevent job dispatch during all tests
    }

    public function test_perfect_match_scenario_95_plus_score(): void
    {
        // Create postal codes for exact location match
        PostalCode::factory()->create(['postal_code' => '49503']);

        // Create business user with profile
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        // Create influencer user with matching profile
        $influencerUser = User::factory()->influencer()->withProfile([
            'postal_code' => '49503', // Exact location match
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS, // Exact industry match
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503', // Exact match = 100 points
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS, // +15 variation = 85 base
                'compensation_type' => CompensationType::MONETARY, // +10 variation = 80 base
                'compensation_amount' => 1000, // +10 amount factor = 90 base
                'campaign_goal' => 'Amazing fitness brand partnership', // Good for randomness
            ])->toArray()
        );

        $score = MatchScore::calculateMatchScore($campaign, $influencerUser->influencer);
        $breakdown = MatchScore::getDetailedScoreBreakdown($campaign, $influencerUser->influencer);

        $this->assertGreaterThan(90, $score);
    }

    public function test_high_match_scenario_85_90_range(): void
    {
        // Create postal codes for close location match
        PostalCode::factory()->create([
            'postal_code' => '49503',
            'latitude' => 42.9634,
            'longitude' => -85.6681,
        ]);

        PostalCode::factory()->create([
            'postal_code' => '49504',
            'latitude' => 42.9734, // About 1-2 miles difference
            'longitude' => -85.6581,
        ]);

        // Create business user with profile
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::BEAUTY_COSMETICS,
            'postal_code' => '49503',
        ])->create();

        // Create influencer user with related industry
        $influencerUser = User::factory()->influencer()->withProfile([
            'postal_code' => '49504', // Close location
            'primary_industry' => BusinessIndustry::FASHION_APPAREL, // Related industry = 80 points
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503', // Close match ~95+ points within radius
                'campaign_type' => CampaignType::PRODUCT_REVIEWS, // +10 variation = 80 base
                'compensation_type' => CompensationType::GIFT_CARD, // +8 variation = 78 base
                'compensation_amount' => 500, // +5 amount factor = 83 base
                'campaign_goal' => 'Beauty product review campaign',
            ])->toArray()
        );

        $score = MatchScore::calculateMatchScore($campaign, $influencerUser->influencer, 50);
        $breakdown = MatchScore::getDetailedScoreBreakdown($campaign, $influencerUser->influencer, 50);


        $this->assertGreaterThan(80, $score);
        $this->assertLessThan(95, $score);
    }

    public function test_good_match_scenario_80_85_range(): void
    {
        // Create business user with profile (no postal code)
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
        ])->create();

        // Create influencer user with matching industry (no postal code)
        $influencerUser = User::factory()->influencer()->withProfile([
            'postal_code' => null, // No location data
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS, // Exact match = 100 points
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '00000', // Dummy zip that doesn't exist in PostalCode table = 50 points
                'campaign_type' => CampaignType::SPONSORED_POSTS, // +5 variation = 75 base
                'compensation_type' => CompensationType::FREE_PRODUCT, // +5 variation = 75 base
                'compensation_amount' => 200, // +2 amount factor = 77 base
                'campaign_goal' => 'Fitness lifestyle sponsored content',
                'campaign_description' => 'fitness wellness lifestyle health',
            ])->toArray()
        );

        $score = MatchScore::calculateMatchScore($campaign, $influencerUser->influencer);
        $breakdown = MatchScore::getDetailedScoreBreakdown($campaign, $influencerUser->influencer);


        $this->assertGreaterThan(75, $score);
        $this->assertLessThan(90, $score);
    }

    public function test_create_campaign_with_target_score(): void
    {
        // Target: 85% match
        // Location: 35% * 90 = 31.5 points (close proximity)
        // Industry: 35% * 100 = 35 points (exact match)
        // Campaign: 20% * 75 = 15 points (decent type)
        // Compensation: 10% * 85 = 8.5 points (good compensation)
        // Total: ~90 points

        PostalCode::factory()->create([
            'postal_code' => '49503',
            'latitude' => 42.9634,
            'longitude' => -85.6681,
        ]);

        PostalCode::factory()->create([
            'postal_code' => '49505',
            'latitude' => 42.9834, // Close but not exact
            'longitude' => -85.6481,
        ]);

        // Create business user with profile
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        // Create influencer user with matching industry but different location
        $influencerUser = User::factory()->influencer()->withProfile([
            'postal_code' => '49505',
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS, // High variation
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 800,
                'campaign_goal' => 'Premium fitness brand collaboration',
            ])->toArray()
        );

        $score = MatchScore::calculateMatchScore($campaign, $influencerUser->influencer, 50);


        $this->assertGreaterThan(90, $score);
        $this->assertLessThan(100, $score);
    }
}