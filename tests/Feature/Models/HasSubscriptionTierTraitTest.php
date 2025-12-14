<?php

namespace Tests\Feature\Models;

use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HasSubscriptionTierTraitTest extends TestCase
{
    use RefreshDatabase;

    private function createSubscribedInfluencer(string $lookupKey): Influencer
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        // Create the stripe product and price
        $product = StripeProduct::factory()->create([
            'billable_type' => Influencer::class,
            'name' => 'Influencer',
        ]);

        $stripePrice = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => $lookupKey,
            'active' => true,
        ]);

        // Create subscription
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

    #[Test]
    public function it_returns_null_tier_when_not_subscribed(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $this->assertNull($influencer->getSubscriptionTier());
    }

    #[Test]
    public function it_returns_professional_tier_for_professional_subscription(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->assertEquals('professional', $influencer->getSubscriptionTier());
    }

    #[Test]
    public function it_returns_elite_tier_for_elite_subscription(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->assertEquals('elite', $influencer->getSubscriptionTier());
    }

    #[Test]
    public function it_correctly_checks_if_on_specific_tier(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->assertTrue($influencer->isOnTier('professional'));
        $this->assertFalse($influencer->isOnTier('elite'));
    }

    #[Test]
    public function it_correctly_checks_tier_hierarchy(): void
    {
        $professionalInfluencer = $this->createSubscribedInfluencer('influencer_professional');
        $eliteInfluencer = $this->createSubscribedInfluencer('influencer_elite');

        // Professional is on professional tier
        $this->assertTrue($professionalInfluencer->isOnTierOrAbove('professional'));
        // Professional is NOT on or above elite tier
        $this->assertFalse($professionalInfluencer->isOnTierOrAbove('elite'));

        // Elite is on or above professional tier
        $this->assertTrue($eliteInfluencer->isOnTierOrAbove('professional'));
        // Elite is on elite tier
        $this->assertTrue($eliteInfluencer->isOnTierOrAbove('elite'));
    }

    #[Test]
    public function it_returns_correct_feature_limits_for_professional(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->assertEquals(3, $influencer->getFeatureLimit('link_in_bio_links'));
        $this->assertFalse($influencer->getFeatureLimit('link_in_bio_customization'));
        $this->assertTrue($influencer->getFeatureLimit('analytics_basic'));
        $this->assertFalse($influencer->getFeatureLimit('analytics_advanced'));
    }

    #[Test]
    public function it_returns_correct_feature_limits_for_elite(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->assertEquals(-1, $influencer->getFeatureLimit('link_in_bio_links'));
        $this->assertTrue($influencer->getFeatureLimit('link_in_bio_customization'));
        $this->assertTrue($influencer->getFeatureLimit('analytics_basic'));
        $this->assertTrue($influencer->getFeatureLimit('analytics_advanced'));
    }

    #[Test]
    public function it_correctly_checks_feature_access(): void
    {
        $professionalInfluencer = $this->createSubscribedInfluencer('influencer_professional');
        $eliteInfluencer = $this->createSubscribedInfluencer('influencer_elite');

        // Professional has link access (limit > 0)
        $this->assertTrue($professionalInfluencer->hasFeatureAccess('link_in_bio_links'));
        // Professional does NOT have customization access
        $this->assertFalse($professionalInfluencer->hasFeatureAccess('link_in_bio_customization'));

        // Elite has link access (unlimited)
        $this->assertTrue($eliteInfluencer->hasFeatureAccess('link_in_bio_links'));
        // Elite has customization access
        $this->assertTrue($eliteInfluencer->hasFeatureAccess('link_in_bio_customization'));
    }

    #[Test]
    public function it_correctly_checks_can_add_more_items(): void
    {
        $professionalInfluencer = $this->createSubscribedInfluencer('influencer_professional');
        $eliteInfluencer = $this->createSubscribedInfluencer('influencer_elite');

        // Professional can add links up to the limit
        $this->assertTrue($professionalInfluencer->canAddMoreItems('link_in_bio_links', 0));
        $this->assertTrue($professionalInfluencer->canAddMoreItems('link_in_bio_links', 2));
        $this->assertFalse($professionalInfluencer->canAddMoreItems('link_in_bio_links', 3));
        $this->assertFalse($professionalInfluencer->canAddMoreItems('link_in_bio_links', 5));

        // Elite can add unlimited links
        $this->assertTrue($eliteInfluencer->canAddMoreItems('link_in_bio_links', 0));
        $this->assertTrue($eliteInfluencer->canAddMoreItems('link_in_bio_links', 100));
        $this->assertTrue($eliteInfluencer->canAddMoreItems('link_in_bio_links', 1000));
    }

    #[Test]
    public function it_returns_tier_required_for_feature(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        // Basic analytics is available at professional
        $this->assertEquals('professional', $influencer->getTierRequiredFor('analytics_basic'));

        // Customization requires elite
        $this->assertEquals('elite', $influencer->getTierRequiredFor('link_in_bio_customization'));
    }

    #[Test]
    public function it_returns_correct_tier_display_name(): void
    {
        $professionalInfluencer = $this->createSubscribedInfluencer('influencer_professional');
        $eliteInfluencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->assertEquals('Professional', $professionalInfluencer->getTierDisplayName());
        $this->assertEquals('Elite', $eliteInfluencer->getTierDisplayName());
    }

    #[Test]
    public function it_returns_next_tier_upgrade(): void
    {
        $professionalInfluencer = $this->createSubscribedInfluencer('influencer_professional');
        $eliteInfluencer = $this->createSubscribedInfluencer('influencer_elite');

        // Professional's next tier is elite
        $this->assertEquals('elite', $professionalInfluencer->getNextTier());

        // Elite has no next tier
        $this->assertNull($eliteInfluencer->getNextTier());
    }

    #[Test]
    public function it_returns_price_lookup_key_for_tier(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->assertEquals('influencer_professional', $influencer->getPriceLookupKeyForTier('professional'));
        $this->assertEquals('influencer_elite', $influencer->getPriceLookupKeyForTier('elite'));
    }

    #[Test]
    public function unsubscribed_user_has_no_feature_access(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $this->assertFalse($influencer->hasFeatureAccess('link_in_bio_links'));
        $this->assertFalse($influencer->hasFeatureAccess('link_in_bio_customization'));
        $this->assertFalse($influencer->canAddMoreItems('link_in_bio_links', 0));
    }
}
