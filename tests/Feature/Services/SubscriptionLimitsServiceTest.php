<?php

namespace Tests\Feature\Services;

use App\Enums\CollaborationStatus;
use App\Facades\SubscriptionLimits;
use App\Models\Business;
use App\Models\Collaboration;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use App\Subscription\SubscriptionMetadataSchema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscriptionLimitsServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createSubscribedInfluencer(array $metadata = []): Influencer
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $product = StripeProduct::factory()->create([
            'billable_type' => Influencer::class,
            'name' => 'Influencer',
        ]);

        $stripePrice = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'influencer_professional',
            'active' => true,
            'metadata' => $metadata,
        ]);

        $influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => $stripePrice->stripe_id,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return $influencer->fresh();
    }

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
    // Credit Management Tests
    // =========================================================================

    #[Test]
    public function it_returns_zero_credits_when_not_subscribed(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $remaining = SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        );

        $this->assertEquals(0, $remaining);
    }

    #[Test]
    public function it_returns_unlimited_when_limit_is_minus_one(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => -1,
        ]);

        $remaining = SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        );

        $this->assertEquals(SubscriptionMetadataSchema::UNLIMITED, $remaining);
    }

    #[Test]
    public function it_initializes_credits_correctly(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 5,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        $this->assertEquals(5, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        ));

        $this->assertEquals(2, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        ));
    }

    #[Test]
    public function it_deducts_credits_correctly(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 5,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        $result = SubscriptionLimits::deductCredit(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        );

        $this->assertTrue($result);
        $this->assertEquals(4, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        ));
    }

    #[Test]
    public function it_cannot_deduct_when_zero_credits(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 1,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        // Deduct the only credit
        SubscriptionLimits::deductCredit(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        );

        // Try to deduct again
        $result = SubscriptionLimits::deductCredit(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        );

        $this->assertFalse($result);
    }

    #[Test]
    public function it_allows_deduction_when_unlimited(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => -1,
        ]);

        $result = SubscriptionLimits::deductCredit(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        );

        // Should return true for unlimited (no actual deduction needed)
        $this->assertTrue($result);
    }

    #[Test]
    public function it_resets_credits_correctly(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 5,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        // Use all credits
        for ($i = 0; $i < 5; $i++) {
            SubscriptionLimits::deductCredit(
                $influencer,
                SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
            );
        }

        $this->assertEquals(0, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        ));

        // Reset credits
        SubscriptionLimits::resetAllCredits($influencer);

        $this->assertEquals(5, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        ));
    }

    #[Test]
    public function it_preserves_excess_credits_during_reset(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 5,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        // Admin grants extra credits (sets to 10, which is more than the plan limit of 5)
        SubscriptionLimits::setCredit(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT,
            10
        );

        $this->assertEquals(10, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        ));

        // Reset credits - should preserve the excess (keep 10, not reset to 5)
        SubscriptionLimits::resetAllCredits($influencer);

        $this->assertEquals(10, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        ));
    }

    #[Test]
    public function it_resets_credits_to_limit_when_below(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 5,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        // Use some credits (down to 2)
        for ($i = 0; $i < 3; $i++) {
            SubscriptionLimits::deductCredit(
                $influencer,
                SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
            );
        }

        $this->assertEquals(2, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        ));

        // Reset credits - should reset to 5 (the plan limit)
        SubscriptionLimits::resetAllCredits($influencer);

        $this->assertEquals(5, SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT
        ));
    }

    // =========================================================================
    // Influencer Application Limit Tests
    // =========================================================================

    #[Test]
    public function influencer_cannot_submit_application_when_not_subscribed(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $this->assertFalse(SubscriptionLimits::canSubmitApplication($influencer));
    }

    #[Test]
    public function influencer_can_submit_application_with_credits(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 5,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        $this->assertTrue(SubscriptionLimits::canSubmitApplication($influencer));
    }

    #[Test]
    public function influencer_cannot_submit_application_when_no_credits(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 1,
        ]);

        SubscriptionLimits::initializeCredits($influencer);
        SubscriptionLimits::deductCredit($influencer, SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT);

        $this->assertFalse(SubscriptionLimits::canSubmitApplication($influencer));
    }

    #[Test]
    public function influencer_can_always_submit_application_when_unlimited(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => -1,
        ]);

        $this->assertTrue(SubscriptionLimits::canSubmitApplication($influencer));
    }

    // =========================================================================
    // Collaboration Limit Tests
    // =========================================================================

    #[Test]
    public function influencer_cannot_start_collaboration_when_not_subscribed(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $this->assertFalse(SubscriptionLimits::canStartCollaboration($influencer));
    }

    #[Test]
    public function influencer_can_start_collaboration_within_limit(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::COLLABORATION_LIMIT => 3,
        ]);

        $this->assertTrue(SubscriptionLimits::canStartCollaboration($influencer));
    }

    #[Test]
    public function influencer_cannot_start_collaboration_at_limit(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::COLLABORATION_LIMIT => 2,
        ]);

        // Create 2 active collaborations
        Collaboration::factory()->count(2)->create([
            'influencer_id' => $influencer->user_id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        $this->assertFalse(SubscriptionLimits::canStartCollaboration($influencer));
    }

    #[Test]
    public function completing_collaboration_frees_slot(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::COLLABORATION_LIMIT => 2,
        ]);

        // Create 2 active collaborations
        $collabs = Collaboration::factory()->count(2)->create([
            'influencer_id' => $influencer->user_id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        $this->assertFalse(SubscriptionLimits::canStartCollaboration($influencer));

        // Complete one collaboration
        $collabs->first()->update(['status' => CollaborationStatus::COMPLETED]);

        $this->assertTrue(SubscriptionLimits::canStartCollaboration($influencer));
    }

    // =========================================================================
    // Business Campaign Publishing Tests
    // =========================================================================

    #[Test]
    public function business_cannot_publish_campaign_when_not_subscribed(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        $this->assertFalse(SubscriptionLimits::canPublishCampaign($business));
    }

    #[Test]
    public function business_can_publish_campaign_with_credits(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT => 3,
        ]);

        SubscriptionLimits::initializeCredits($business);

        $this->assertTrue(SubscriptionLimits::canPublishCampaign($business));
    }

    #[Test]
    public function business_cannot_publish_campaign_when_no_credits(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT => 1,
        ]);

        SubscriptionLimits::initializeCredits($business);
        SubscriptionLimits::deductCredit($business, SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT);

        $this->assertFalse(SubscriptionLimits::canPublishCampaign($business));
    }

    // =========================================================================
    // Business Campaign Boost Tests
    // =========================================================================

    #[Test]
    public function business_cannot_boost_campaign_when_not_subscribed(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        $this->assertFalse(SubscriptionLimits::canBoostCampaign($business));
    }

    #[Test]
    public function business_can_boost_campaign_with_credits(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($business);

        $this->assertTrue(SubscriptionLimits::canBoostCampaign($business));
    }

    #[Test]
    public function business_cannot_boost_campaign_when_no_credits(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS => 1,
        ]);

        SubscriptionLimits::initializeCredits($business);
        SubscriptionLimits::deductCredit($business, SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS);

        $this->assertFalse(SubscriptionLimits::canBoostCampaign($business));
    }

    // =========================================================================
    // Profile Promotion Tests
    // =========================================================================

    #[Test]
    public function influencer_can_promote_profile_with_credits(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        $this->assertTrue(SubscriptionLimits::canPromoteProfile($influencer));
    }

    #[Test]
    public function business_can_promote_profile_with_credits(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($business);

        $this->assertTrue(SubscriptionLimits::canPromoteProfile($business));
    }

    // =========================================================================
    // Team Member Tests
    // =========================================================================

    #[Test]
    public function business_cannot_invite_team_member_with_zero_limit(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT => 0,
        ]);

        $this->assertFalse(SubscriptionLimits::canInviteTeamMember($business));
    }

    #[Test]
    public function business_can_invite_team_member_within_limit(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT => 3,
        ]);

        $this->assertTrue(SubscriptionLimits::canInviteTeamMember($business));
    }

    // =========================================================================
    // Limits Summary Tests
    // =========================================================================

    #[Test]
    public function it_returns_correct_limits_summary_for_influencer(): void
    {
        $influencer = $this->createSubscribedInfluencer([
            SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT => 5,
            SubscriptionMetadataSchema::COLLABORATION_LIMIT => 3,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($influencer);

        $summary = SubscriptionLimits::getLimitsSummary($influencer);

        $this->assertArrayHasKey(SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT, $summary);
        $this->assertEquals(5, $summary[SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT]['limit']);
        $this->assertEquals(5, $summary[SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT]['remaining']);
    }

    #[Test]
    public function it_returns_correct_limits_summary_for_business(): void
    {
        $business = $this->createSubscribedBusiness([
            SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT => 3,
            SubscriptionMetadataSchema::COLLABORATION_LIMIT => 8,
            SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS => 2,
        ]);

        SubscriptionLimits::initializeCredits($business);

        $summary = SubscriptionLimits::getLimitsSummary($business);

        $this->assertArrayHasKey(SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT, $summary);
        $this->assertEquals(3, $summary[SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT]['limit']);
    }
}
