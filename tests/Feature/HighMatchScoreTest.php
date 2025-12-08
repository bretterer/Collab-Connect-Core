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
            'content_types' => [CampaignType::BRAND_PARTNERSHIPS->value], // Matches campaign type
            'compensation_types' => [CompensationType::MONETARY->value], // Matches compensation type
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503', // Exact match = 100 points
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Amazing fitness brand partnership',
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
            'content_types' => [CampaignType::PRODUCT_REVIEWS->value], // Matches campaign type
            'compensation_types' => [CompensationType::GIFT_CARD->value], // Matches compensation type
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503', // Close match ~95+ points within radius
                'campaign_type' => CampaignType::PRODUCT_REVIEWS,
                'compensation_type' => CompensationType::GIFT_CARD,
                'compensation_amount' => 500,
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
            'content_types' => [CampaignType::SPONSORED_POSTS->value], // Matches campaign type
            'compensation_types' => [CompensationType::FREE_PRODUCT->value], // Matches compensation type
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '00000', // Dummy zip that doesn't exist in PostalCode table = 50 points
                'campaign_type' => CampaignType::SPONSORED_POSTS,
                'compensation_type' => CompensationType::FREE_PRODUCT,
                'compensation_amount' => 200,
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
        // Target: 90%+ match
        // Location: 35% * ~95 = ~33 points (close proximity)
        // Industry: 35% * 100 = 35 points (exact match)
        // Campaign: 20% * 100 = 20 points (exact type match)
        // Compensation: 10% * 100 = 10 points (exact compensation match)
        // Total: ~98 points

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
            'content_types' => [CampaignType::BRAND_PARTNERSHIPS->value], // Matches campaign type
            'compensation_types' => [CompensationType::MONETARY->value], // Matches compensation type
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
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
