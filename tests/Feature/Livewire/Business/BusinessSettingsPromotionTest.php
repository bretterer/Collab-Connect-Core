<?php

namespace Tests\Feature\Livewire\Business;

use App\Livewire\Business\BusinessSettings;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
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
        $user->currentBusiness->update(['promotion_credits' => 3]);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertSet('promotion_credits', 3)
            ->assertSet('is_promoted', false)
            ->call('promoteProfile')
            ->assertSet('is_promoted', true)
            ->assertSet('promotion_credits', 2);

        $user->refresh();
        $this->assertTrue($user->currentBusiness->is_promoted);
        $this->assertEquals(2, $user->currentBusiness->promotion_credits);
        $this->assertNotNull($user->currentBusiness->promoted_until);
    }

    #[Test]
    public function business_cannot_promote_profile_without_credits(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $user->currentBusiness->update(['promotion_credits' => 0]);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertSet('promotion_credits', 0)
            ->call('promoteProfile')
            ->assertSet('is_promoted', false);

        $user->refresh();
        $this->assertFalse($user->currentBusiness->is_promoted);
    }

    #[Test]
    public function business_promotion_lasts_seven_days(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $user->currentBusiness->update(['promotion_credits' => 1]);

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
        $user->currentBusiness->update(['promotion_credits' => 5]);

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->assertSet('promotion_credits', 5)
            ->assertSee('5 credits available');
    }

    #[Test]
    public function business_sees_buy_credits_button_when_no_credits(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $user->currentBusiness->update(['promotion_credits' => 0]);

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
    public function promo_credit_price_computed_property_returns_active_price(): void
    {
        $user = User::factory()->business()->withProfile()->create();

        $product = StripeProduct::factory()->create();
        $price = StripePrice::factory()->oneTime()->forProduct($product)->create([
            'lookup_key' => 'profile_promo_credit_current',
            'active' => true,
            'unit_amount' => 999,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(BusinessSettings::class);
        $promoCreditPrice = $component->get('promoCreditPrice');
        $this->assertEquals($price->id, $promoCreditPrice->id);
        $this->assertEquals(999, $promoCreditPrice->unit_amount);
    }

    #[Test]
    public function promo_credit_price_returns_null_when_no_active_price(): void
    {
        $user = User::factory()->business()->withProfile()->create();

        $product = StripeProduct::factory()->create();
        StripePrice::factory()->oneTime()->forProduct($product)->create([
            'lookup_key' => 'profile_promo_credit_current',
            'active' => false,
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

        $product = StripeProduct::factory()->create();
        StripePrice::factory()->oneTime()->forProduct($product)->create([
            'lookup_key' => 'profile_promo_credit_current',
            'active' => true,
            'unit_amount' => 999,
        ]);

        $this->actingAs($user);

        // Test quantity too low
        Livewire::test(BusinessSettings::class)
            ->set('creditQuantity', 0)
            ->call('purchasePromotionCredits');

        $user->refresh();
        $this->assertEquals(0, $user->currentBusiness->promotion_credits);

        // Test quantity too high
        Livewire::test(BusinessSettings::class)
            ->set('creditQuantity', 11)
            ->call('purchasePromotionCredits');

        $user->refresh();
        $this->assertEquals(0, $user->currentBusiness->promotion_credits);
    }

    #[Test]
    public function purchase_credits_fails_when_price_not_available(): void
    {
        $user = User::factory()->business()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(BusinessSettings::class)
            ->set('creditQuantity', 1)
            ->call('purchasePromotionCredits');

        $user->refresh();
        $this->assertEquals(0, $user->currentBusiness->promotion_credits);
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
