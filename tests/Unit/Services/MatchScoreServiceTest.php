<?php

namespace Tests\Unit\Services;

use App\Enums\BusinessIndustry;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Models\Campaign;
use App\Models\User;
use App\Services\MatchScoreService;
use Tests\TestCase;

class MatchScoreServiceTest extends TestCase
{
    private MatchScoreService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MatchScoreService;
    }

    public function test_calculates_perfect_match_score(): void
    {
        $campaign = Campaign::factory()->make([
            'target_zip_code' => '49503',
            'campaign_type' => CampaignType::PRODUCT_REVIEWS,
            'compensation_type' => CompensationType::MONETARY,
            'compensation_amount' => 500,
        ]);

        $campaign->setRelation('business', User::factory()->make([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
        ]));

        $influencerProfile = (object) [
            'postal_code' => '49503',
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
        ];

        $score = $this->service->calculateMatchScore($campaign, $influencerProfile);

        $this->assertGreaterThan(90, $score);
        $this->assertLessThanOrEqual(100, $score);
    }

    public function test_calculates_location_score_exact_match(): void
    {
        $campaign = Campaign::factory()->make(['target_zip_code' => '49503']);
        $influencerProfile = (object) ['postal_code' => '49503'];

        $score = $this->service->calculateLocationScore($campaign, $influencerProfile, 50);

        $this->assertEquals(100.0, $score);
    }

    public function test_calculates_location_score_missing_data(): void
    {
        $campaign = Campaign::factory()->make(['target_zip_code' => null]);
        $influencerProfile = (object) ['postal_code' => '49503'];

        $score = $this->service->calculateLocationScore($campaign, $influencerProfile, 50);

        $this->assertEquals(50.0, $score);
    }

    public function test_calculates_industry_score_exact_match(): void
    {
        $campaign = Campaign::factory()->make();
        $campaign->setRelation('business', User::factory()->make([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
        ]));

        $influencerProfile = (object) [
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
        ];

        $score = $this->service->calculateIndustryScore($campaign, $influencerProfile);

        $this->assertEquals(100.0, $score);
    }

    public function test_calculates_industry_score_related_industries(): void
    {
        $campaign = Campaign::factory()->make();
        $campaign->setRelation('business', User::factory()->make([
            'industry' => BusinessIndustry::BEAUTY_COSMETICS,
        ]));

        $influencerProfile = (object) [
            'primary_industry' => BusinessIndustry::FASHION_APPAREL,
        ];

        $score = $this->service->calculateIndustryScore($campaign, $influencerProfile);

        $this->assertEquals(80.0, $score);
    }

    public function test_calculates_campaign_type_score(): void
    {
        $campaign = Campaign::factory()->make([
            'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
            'campaign_goal' => 'Test campaign goal',
        ]);

        $influencerProfile = (object) [];

        $score = $this->service->calculateCampaignTypeScore($campaign, $influencerProfile);

        $this->assertGreaterThanOrEqual(50.0, $score);
        $this->assertLessThanOrEqual(90.0, $score);
    }

    public function test_calculates_compensation_score(): void
    {
        $campaign = Campaign::factory()->make([
            'compensation_type' => CompensationType::MONETARY,
            'compensation_amount' => 1000,
            'campaign_goal' => 'Test campaign goal',
        ]);

        $influencerProfile = (object) [];

        $score = $this->service->calculateCompensationScore($campaign, $influencerProfile);

        $this->assertGreaterThanOrEqual(50.0, $score);
        $this->assertLessThanOrEqual(90.0, $score);
    }

    public function test_gets_detailed_score_breakdown(): void
    {
        $campaign = Campaign::factory()->make([
            'target_zip_code' => '49503',
            'campaign_type' => CampaignType::PRODUCT_REVIEWS,
            'compensation_type' => CompensationType::MONETARY,
            'compensation_amount' => 500,
            'campaign_goal' => 'Test campaign',
        ]);

        $campaign->setRelation('business', User::factory()->make([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
        ]));

        $influencerProfile = (object) [
            'postal_code' => '49503',
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
        ];

        $breakdown = $this->service->getDetailedScoreBreakdown($campaign, $influencerProfile);

        $this->assertArrayHasKey('location', $breakdown);
        $this->assertArrayHasKey('industry', $breakdown);
        $this->assertArrayHasKey('campaign_type', $breakdown);
        $this->assertArrayHasKey('compensation', $breakdown);
        $this->assertArrayHasKey('total', $breakdown);

        $this->assertArrayHasKey('raw', $breakdown['location']);
        $this->assertArrayHasKey('weighted', $breakdown['location']);
        $this->assertArrayHasKey('weight', $breakdown['location']);
    }
}
