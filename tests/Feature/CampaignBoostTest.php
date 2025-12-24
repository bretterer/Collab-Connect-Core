<?php

namespace Tests\Feature;

use App\Facades\SubscriptionLimits;
use App\Jobs\HandleCampaignBoostUpdates;
use App\Livewire\Campaigns\ShowCampaign;
use App\Models\Business;
use App\Models\Campaign;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use App\Services\MatchScoreService;
use App\Subscription\SubscriptionMetadataSchema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CampaignBoostTest extends TestCase
{
    use RefreshDatabase;

    private function createSubscribedBusiness(array $metadata = []): Business
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        $product = StripeProduct::factory()->create([
            'billable_type' => Business::class,
            'name' => 'Business',
        ]);

        $stripePrice = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'business_professional',
            'active' => true,
            'metadata' => $metadata,
        ]);

        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => $stripePrice->stripe_id,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return $business->fresh();
    }

    // =========================================================================
    // Campaign Model Tests
    // =========================================================================

    #[Test]
    public function campaign_is_not_boosted_by_default(): void
    {
        $campaign = Campaign::factory()->create();

        $this->assertFalse($campaign->isBoosted());
        $this->assertNull($campaign->boostDaysRemaining());
    }

    #[Test]
    public function campaign_is_boosted_when_flag_is_true_and_date_is_future(): void
    {
        $campaign = Campaign::factory()->published()->boosted(7)->create();

        $this->assertTrue($campaign->isBoosted());
        $this->assertGreaterThan(0, $campaign->boostDaysRemaining());
    }

    #[Test]
    public function campaign_is_not_boosted_when_date_is_past(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'is_boosted' => true,
            'boosted_until' => now()->subDay(),
        ]);

        $this->assertFalse($campaign->isBoosted());
    }

    #[Test]
    public function boost_days_remaining_returns_correct_value(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'is_boosted' => true,
            'boosted_until' => now()->addDays(5)->endOfDay(),
        ]);

        // Allow for slight timing differences (4-5 days)
        $remaining = $campaign->boostDaysRemaining();
        $this->assertGreaterThanOrEqual(4, $remaining);
        $this->assertLessThanOrEqual(5, $remaining);
    }

    // =========================================================================
    // Match Score Boost Tests
    // =========================================================================

    #[Test]
    public function boosted_campaign_gets_match_score_bonus(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create()->influencer;
        $business = Business::factory()->create();

        // Create same base campaign data
        $campaignData = [
            'business_id' => $business->id,
            'target_zip_code' => '49503',
            'campaign_goal' => 'Test Goal',
        ];

        // Create a non-boosted campaign
        $normalCampaign = Campaign::factory()->published()->create($campaignData);

        // Calculate score for non-boosted campaign
        $service = app(MatchScoreService::class);
        $normalScore = $service->calculateMatchScore($normalCampaign, $influencer);

        // Now boost the same campaign
        $normalCampaign->update([
            'is_boosted' => true,
            'boosted_until' => now()->addDays(7),
        ]);
        $normalCampaign->refresh();

        $boostedScore = $service->calculateMatchScore($normalCampaign, $influencer);

        // Boosted score should be exactly 5 points higher (or max 100)
        $expectedBoostedScore = min($normalScore + MatchScoreService::BOOST_BONUS, 100);
        $this->assertEquals($expectedBoostedScore, $boostedScore);
    }

    #[Test]
    public function boost_bonus_is_five_percent(): void
    {
        $this->assertEquals(5.0, MatchScoreService::BOOST_BONUS);
    }

    // =========================================================================
    // Handle Campaign Boost Updates Job Tests
    // =========================================================================

    #[Test]
    public function job_expires_boosted_campaigns_with_past_date(): void
    {
        $expiredCampaign = Campaign::factory()->published()->create([
            'is_boosted' => true,
            'boosted_until' => now()->subHour(),
        ]);

        $activeCampaign = Campaign::factory()->published()->boosted(7)->create();

        $job = new HandleCampaignBoostUpdates;
        $job->handle();

        $expiredCampaign->refresh();
        $activeCampaign->refresh();

        $this->assertFalse($expiredCampaign->is_boosted);
        $this->assertNull($expiredCampaign->boosted_until);

        $this->assertTrue($activeCampaign->is_boosted);
        $this->assertNotNull($activeCampaign->boosted_until);
    }

    #[Test]
    public function job_does_nothing_when_no_expired_boosts(): void
    {
        $activeCampaign = Campaign::factory()->published()->boosted(7)->create();

        $job = new HandleCampaignBoostUpdates;
        $job->handle();

        $activeCampaign->refresh();

        $this->assertTrue($activeCampaign->is_boosted);
    }

    // =========================================================================
    // Livewire Component Tests
    // =========================================================================

    #[Test]
    public function owner_can_boost_published_campaign(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($business);

        $user = $business->members->first();
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $business->id,
        ]);

        $this->actingAs($user);

        Livewire::test(ShowCampaign::class, ['campaign' => $campaign])
            ->call('boostCampaign')
            ->assertHasNoErrors();

        $campaign->refresh();

        $this->assertTrue($campaign->isBoosted());
        $this->assertEquals(1, SubscriptionLimits::getRemainingCredits(
            $business,
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS
        ));
    }

    #[Test]
    public function cannot_boost_draft_campaign(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($business);

        $user = $business->members->first();
        $campaign = Campaign::factory()->create([
            'business_id' => $business->id,
        ]);

        $this->actingAs($user);

        Livewire::test(ShowCampaign::class, ['campaign' => $campaign])
            ->call('boostCampaign');

        $campaign->refresh();

        $this->assertFalse($campaign->isBoosted());
    }

    #[Test]
    public function cannot_boost_already_boosted_campaign(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($business);

        $user = $business->members->first();
        $campaign = Campaign::factory()->published()->boosted()->create([
            'business_id' => $business->id,
        ]);

        $this->actingAs($user);

        Livewire::test(ShowCampaign::class, ['campaign' => $campaign])
            ->call('boostCampaign');

        // Credit should not have been deducted
        $this->assertEquals(2, SubscriptionLimits::getRemainingCredits(
            $business,
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS
        ));
    }

    #[Test]
    public function cannot_boost_when_no_credits_remaining(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS => 1,
        ]);

        SubscriptionLimits::initializeCredits($business);
        SubscriptionLimits::deductCredit($business, SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS);

        $user = $business->members->first();
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $business->id,
        ]);

        $this->actingAs($user);

        Livewire::test(ShowCampaign::class, ['campaign' => $campaign])
            ->call('boostCampaign');

        $campaign->refresh();

        $this->assertFalse($campaign->isBoosted());
    }

    // =========================================================================
    // Campaign Scopes Tests
    // =========================================================================

    #[Test]
    public function boosted_scope_returns_only_active_boosts(): void
    {
        Campaign::factory()->published()->boosted()->create();
        Campaign::factory()->published()->create();
        Campaign::factory()->published()->create([
            'is_boosted' => true,
            'boosted_until' => now()->subDay(),
        ]);

        $boostedCampaigns = Campaign::boosted()->get();

        $this->assertCount(1, $boostedCampaigns);
    }

    #[Test]
    public function boost_expired_scope_returns_expired_boosts(): void
    {
        Campaign::factory()->published()->boosted()->create();
        Campaign::factory()->published()->create([
            'is_boosted' => true,
            'boosted_until' => now()->subDay(),
        ]);

        $expiredCampaigns = Campaign::boostExpired()->get();

        $this->assertCount(1, $expiredCampaigns);
    }
}
