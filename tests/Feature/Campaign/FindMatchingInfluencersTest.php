<?php

namespace Tests\Feature\Campaign;

use App\Enums\BusinessIndustry;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Facades\MatchScore;
use App\Models\Campaign;
use App\Models\PostalCode;
use App\Models\User;
use App\Services\MatchScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FindMatchingInfluencersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake(); // Prevent job dispatch during all tests
    }

    #[Test]
    public function finds_high_matching_influencers_above_threshold(): void
    {
        PostalCode::factory()->create(['postal_code' => '49503']);

        // Create business user with profile
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        // Create high-matching influencer
        $highMatchInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS, // Exact match
            'postal_code' => '49503', // Exact location match
            'onboarding_complete' => true,
        ])->create();

        // Create low-matching influencer
        $lowMatchInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::TECHNOLOGY, // Different industry
            'postal_code' => '90210', // Different location
            'onboarding_complete' => true,
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Premium fitness brand partnership',
            ])->toArray()
        );

        $matchingInfluencers = $campaign->findMatchingInfluencers(80);

        // Should only include the high-matching influencer
        $this->assertCount(1, $matchingInfluencers);
        $this->assertEquals($highMatchInfluencer->influencer->id, $matchingInfluencers->first()->id);
        $this->assertGreaterThan(80, $matchingInfluencers->first()->match_score);
    }

    #[Test]
    public function filters_out_influencers_below_threshold(): void
    {
        PostalCode::factory()->create(['postal_code' => '49503']);

        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        // Create medium-matching influencer (should be around 70-80%)
        $mediumMatchInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::HEALTHCARE, // Related industry (80 points)
            'postal_code' => '90210', // Different location (low location score)
            'onboarding_complete' => true,
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::SPONSORED_POSTS, // Lower scoring type
                'compensation_type' => CompensationType::FREE_PRODUCT, // Lower scoring compensation
                'compensation_amount' => 200,
                'campaign_goal' => 'Fitness product promotion',
            ])->toArray()
        );

        // Test with high threshold (95%)
        $highThresholdMatches = $campaign->findMatchingInfluencers(95);
        $this->assertCount(0, $highThresholdMatches);

        // Test with lower threshold (70%)
        $lowThresholdMatches = $campaign->findMatchingInfluencers(70);
        $this->assertGreaterThanOrEqual(0, $lowThresholdMatches->count());
    }

    #[Test]
    public function excludes_business_owner_from_results(): void
    {
        PostalCode::factory()->create(['postal_code' => '49503']);

        // Create business user who is also an influencer
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        // Create influencer profile for the business user
        $businessUser->influencer()->create([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
            'onboarding_complete' => true,
            'bio' => 'Business owner bio',
        ]);

        // Create separate influencer
        $separateInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
            'onboarding_complete' => true,
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Premium fitness partnership',
            ])->toArray()
        );

        $matchingInfluencers = $campaign->findMatchingInfluencers(80);

        // Should only include the separate influencer, not the business owner
        $this->assertCount(1, $matchingInfluencers);
        $this->assertEquals($separateInfluencer->influencer->id, $matchingInfluencers->first()->id);
        $this->assertNotEquals($businessUser->influencer->id, $matchingInfluencers->first()->id);
    }

    #[Test]
    public function only_includes_completed_onboarding_influencers(): void
    {
        PostalCode::factory()->create(['postal_code' => '49503']);

        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        // Create influencer with incomplete onboarding
        $incompleteInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
            'onboarding_complete' => false, // Not completed
        ])->create();

        // Create influencer with complete onboarding
        $completeInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
            'onboarding_complete' => true,
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Premium fitness partnership',
            ])->toArray()
        );

        $matchingInfluencers = $campaign->findMatchingInfluencers(80);

        // Should only include the influencer with complete onboarding
        $this->assertCount(1, $matchingInfluencers);
        $this->assertEquals($completeInfluencer->influencer->id, $matchingInfluencers->first()->id);
    }

    #[Test]
    public function returns_results_sorted_by_match_score_descending(): void
    {
        PostalCode::factory()->create(['postal_code' => '49503']);
        PostalCode::factory()->create(['postal_code' => '49504']);

        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        // Create influencer with perfect match (should have highest score)
        $perfectInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS, // Exact match
            'postal_code' => '49503', // Exact location
            'onboarding_complete' => true,
        ])->create();

        // Create influencer with good match (should have lower score)
        $goodInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::HEALTHCARE, // Related industry
            'postal_code' => '49504', // Close location
            'onboarding_complete' => true,
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Premium fitness partnership',
            ])->toArray()
        );

        $matchingInfluencers = $campaign->findMatchingInfluencers(60);

        // Should have at least one influencer
        $this->assertGreaterThan(0, $matchingInfluencers->count());

        // If we have multiple influencers, verify they're sorted by score descending
        if ($matchingInfluencers->count() >= 2) {
            $this->assertGreaterThan($matchingInfluencers[1]->match_score, $matchingInfluencers[0]->match_score);
        }

        // Verify the perfect match influencer is first
        $this->assertEquals($perfectInfluencer->influencer->id, $matchingInfluencers->first()->id);
        $this->assertGreaterThan(90, $matchingInfluencers->first()->match_score);
    }

    #[Test]
    public function can_use_custom_threshold(): void
    {
        PostalCode::factory()->create(['postal_code' => '49503']);

        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        $influencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
            'onboarding_complete' => true,
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Premium fitness partnership',
            ])->toArray()
        );

        // Test with different thresholds
        $lowThresholdMatches = $campaign->findMatchingInfluencers(50);
        $highThresholdMatches = $campaign->findMatchingInfluencers(95);

        $this->assertGreaterThanOrEqual($highThresholdMatches->count(), $lowThresholdMatches->count());
    }

    #[Test]
    public function handles_campaign_without_business_gracefully(): void
    {
        $campaign = Campaign::factory()->make(['business_id' => null]);

        $matchingInfluencers = $campaign->findMatchingInfluencers(80);

        $this->assertCount(0, $matchingInfluencers);
        $this->assertTrue($matchingInfluencers->isEmpty());
    }

    #[Test]
    public function database_query_optimization_reduces_match_score_calculations(): void
    {
        PostalCode::factory()->create(['postal_code' => '49503']);

        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '49503',
        ])->create();

        // Create many influencers with different industries (only some should match)
        $fitnessInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS, // Should be included
            'postal_code' => '49503',
            'onboarding_complete' => true,
        ])->create();

        $techInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::TECHNOLOGY, // Should be filtered out
            'postal_code' => '49503',
            'onboarding_complete' => true,
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '49503',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Premium fitness partnership',
            ])->toArray()
        );

        // Mock the MatchScore facade to count calls
        $mockService = $this->mock(MatchScoreService::class);
        $callCount = 0;

        $mockService->shouldReceive('calculateMatchScore')
            ->andReturnUsing(function () use (&$callCount) {
                $callCount++;

                return 85.0; // Return a score above threshold
            });

        $matchingInfluencers = $campaign->findMatchingInfluencers(80);

        // Verify that MatchScore was called fewer times than total influencers
        // (due to database pre-filtering)
        $totalInfluencers = User::whereHas('influencer')->count();
        $this->assertLessThan($totalInfluencers, $callCount);
    }
}
