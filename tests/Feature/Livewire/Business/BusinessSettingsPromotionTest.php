<?php

namespace Tests\Feature\Livewire\Business;

use App\Facades\SubscriptionLimits;
use App\Livewire\Business\BusinessSettings;
use App\Models\AddonPriceMapping;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use App\Subscription\SubscriptionMetadataSchema;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BusinessSettingsPromotionTest extends TestCase
{
    #[Test]
    public function business_can_view_promotion_section(): void
    {
        $user = User::factory()->business()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertStatus(200)
            ->assertSee('Promote Profile');
    }

    #[Test]
    public function business_can_promote_profile_with_credits(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        // Create subscription and credits using the new system
        $price = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 5,
            ],
        ]);
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $price->stripe_id,
        ]);
        SubscriptionLimits::initializeCredits($business);

        // Verify initial credits
        $initialCredits = SubscriptionLimits::getRemainingCredits(
            $business,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertSet('is_promoted', false)
            ->call('promoteProfile')
            ->assertSet('is_promoted', true);

        $user->refresh();
        $this->assertTrue($user->currentBusiness->is_promoted);
        $this->assertNotNull($user->currentBusiness->promoted_until);

        // Verify credit was deducted
        $remainingCredits = SubscriptionLimits::getRemainingCredits(
            $business,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );
        $this->assertEquals($initialCredits - 1, $remainingCredits);
    }

    #[Test]
    public function business_cannot_promote_profile_without_credits(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        // Create subscription with 0 credits
        $price = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 0,
            ],
        ]);
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $price->stripe_id,
        ]);
        SubscriptionLimits::initializeCredits($business);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->call('promoteProfile')
            ->assertSet('is_promoted', false);

        $user->refresh();
        $this->assertFalse($user->currentBusiness->is_promoted);
    }

    #[Test]
    public function business_promotion_lasts_seven_days(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        // Create subscription with credits
        $price = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 1,
            ],
        ]);
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $price->stripe_id,
        ]);
        SubscriptionLimits::initializeCredits($business);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->call('promoteProfile');

        $user->refresh();
        $expectedEndDate = now()->addDays(7)->startOfDay();
        $actualEndDate = $user->currentBusiness->promoted_until->startOfDay();

        $this->assertEquals($expectedEndDate->toDateString(), $actualEndDate->toDateString());
    }

    #[Test]
    public function business_sees_promotion_credits_count(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        // Create subscription with 5 credits
        $price = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 5,
            ],
        ]);
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $price->stripe_id,
        ]);
        SubscriptionLimits::initializeCredits($business);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertSet('promotion_credits', 5)
            ->assertSee('5 credits available');
    }

    #[Test]
    public function business_sees_buy_credits_button_when_no_credits(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        // Create subscription with 0 credits
        $price = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 0,
            ],
        ]);
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $price->stripe_id,
        ]);
        SubscriptionLimits::initializeCredits($business);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertSee('Buy Promotion Credits');
    }

    #[Test]
    public function business_sees_currently_promoted_status(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $user->currentBusiness->update([
            'is_promoted' => true,
            'promoted_until' => now()->addDays(5),
        ]);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertSet('is_promoted', true)
            ->assertSee('Currently Promoted until');
    }

    #[Test]
    public function promo_credit_price_computed_property_returns_active_mapping(): void
    {
        $user = User::factory()->business()->withProfile()->create();

        $product = StripeProduct::factory()->create();
        $price = StripePrice::factory()->oneTime()->forProduct($product)->create([
            'active' => true,
            'unit_amount' => 999,
        ]);

        // Create addon mapping
        $mapping = AddonPriceMapping::create([
            'stripe_price_id' => $price->id,
            'credit_key' => SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS,
            'credits_granted' => 1,
            'account_type' => 'both',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(BusinessSettings::class);
        $promoCreditPrice = $component->get('promoCreditPrice');
        $this->assertEquals($mapping->id, $promoCreditPrice->id);
        $this->assertEquals(999, $promoCreditPrice->stripePrice->unit_amount);
    }

    #[Test]
    public function promo_credit_price_returns_null_when_no_active_mapping(): void
    {
        $user = User::factory()->business()->withProfile()->create();

        $product = StripeProduct::factory()->create();
        $price = StripePrice::factory()->oneTime()->forProduct($product)->create([
            'active' => true,
        ]);

        // Create inactive addon mapping
        AddonPriceMapping::create([
            'stripe_price_id' => $price->id,
            'credit_key' => SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS,
            'credits_granted' => 1,
            'account_type' => 'both',
            'is_active' => false,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(BusinessSettings::class);
        $this->assertNull($component->get('promoCreditPrice'));
    }

    #[Test]
    public function business_can_select_credit_quantity(): void
    {
        $user = User::factory()->business()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertSet('creditQuantity', 1)
            ->set('creditQuantity', 5)
            ->assertSet('creditQuantity', 5);
    }

    #[Test]
    public function purchase_credits_fails_with_invalid_quantity(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        $product = StripeProduct::factory()->create();
        $price = StripePrice::factory()->oneTime()->forProduct($product)->create([
            'active' => true,
            'unit_amount' => 999,
        ]);

        // Create addon mapping
        AddonPriceMapping::create([
            'stripe_price_id' => $price->id,
            'credit_key' => SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS,
            'credits_granted' => 1,
            'account_type' => 'both',
            'is_active' => true,
        ]);

        // Create subscription with 0 credits to start
        $subscriptionPrice = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 0,
            ],
        ]);
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $subscriptionPrice->stripe_id,
        ]);
        SubscriptionLimits::initializeCredits($business);

        $this->actingAs($user);

        // Test quantity too low
        Livewire::test(BusinessSettings::class)
            ->set('creditQuantity', 0)
            ->call('purchasePromotionCredits');

        $remainingCredits = SubscriptionLimits::getRemainingCredits(
            $business,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );
        $this->assertEquals(0, $remainingCredits);

        // Test quantity too high
        Livewire::test(BusinessSettings::class)
            ->set('creditQuantity', 11)
            ->call('purchasePromotionCredits');

        $remainingCredits = SubscriptionLimits::getRemainingCredits(
            $business,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );
        $this->assertEquals(0, $remainingCredits);
    }

    #[Test]
    public function purchase_credits_fails_when_no_mapping_available(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        // Create subscription with 0 credits
        $subscriptionPrice = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => 0,
            ],
        ]);
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $subscriptionPrice->stripe_id,
        ]);
        SubscriptionLimits::initializeCredits($business);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->set('creditQuantity', 1)
            ->call('purchasePromotionCredits');

        $remainingCredits = SubscriptionLimits::getRemainingCredits(
            $business,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );
        $this->assertEquals(0, $remainingCredits);
    }

    #[Test]
    public function influencer_cannot_access_business_settings(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertRedirect(route('dashboard'));
    }
}
