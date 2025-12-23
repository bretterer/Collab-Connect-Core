<?php

namespace Tests\Feature\Livewire\Influencer;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Livewire\Influencer\InfluencerSettings;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InfluencerSettingsTest extends TestCase
{
    #[Test]
    public function influencer_can_view_settings_page(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertStatus(200)
            ->assertSee('Your Profile');
    }

    #[Test]
    public function business_user_is_redirected_from_influencer_settings(): void
    {
        $user = User::factory()->business()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function influencer_can_update_settings(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        // Save account tab settings
        Livewire::test(InfluencerSettings::class)
            ->set('username', 'testinfluencer')
            ->set('bio', 'This is my updated bio for testing purposes.')
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
            ->call('updateInfluencerSettings')
            ->assertHasNoErrors();

        // Save match tab settings
        Livewire::test(InfluencerSettings::class)
            ->call('setActiveTab', 'match')
            ->set('primary_industry', BusinessIndustry::FOOD_BEVERAGE->value)
            ->set('typical_lead_time_days', 7)
            ->call('updateInfluencerSettings')
            ->assertHasNoErrors();

        $user->refresh();
        $influencer = $user->influencer;

        $this->assertEquals('testinfluencer', $influencer->username);
        $this->assertEquals('This is my updated bio for testing purposes.', $influencer->bio);
        $this->assertEquals(BusinessIndustry::FOOD_BEVERAGE, $influencer->primary_industry);
        $this->assertEquals('Springfield', $influencer->city);
        $this->assertEquals('OH', $influencer->state);
        $this->assertEquals('45066', $influencer->postal_code);
        $this->assertEquals('555-123-4567', $influencer->phone_number);
        $this->assertEquals(7, $influencer->typical_lead_time_days);
    }

    #[Test]
    public function influencer_can_update_content_types(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        $contentTypes = [
            CampaignType::SPONSORED_POSTS->value,
            CampaignType::PRODUCT_REVIEWS->value,
        ];

        Livewire::test(InfluencerSettings::class)
            ->call('setActiveTab', 'match')
            ->set('content_types', $contentTypes)
            ->set('typical_lead_time_days', 7)
            ->call('updateInfluencerSettings')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertEquals($contentTypes, $user->influencer->content_types);
    }

    #[Test]
    public function influencer_can_update_preferred_business_types(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        $businessTypes = [
            BusinessType::RESTAURANT->value,
        ];

        Livewire::test(InfluencerSettings::class)
            ->call('setActiveTab', 'match')
            ->set('preferred_business_types', $businessTypes)
            ->set('typical_lead_time_days', 7)
            ->call('updateInfluencerSettings')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertEquals($businessTypes, $user->influencer->preferred_business_types);
    }

    #[Test]
    public function influencer_can_update_compensation_types(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        $compensationTypes = [
            CompensationType::MONETARY->value,
            CompensationType::FREE_PRODUCT->value,
        ];

        Livewire::test(InfluencerSettings::class)
            ->call('setActiveTab', 'match')
            ->set('compensation_types', $compensationTypes)
            ->set('typical_lead_time_days', 7)
            ->call('updateInfluencerSettings')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertEquals($compensationTypes, $user->influencer->compensation_types);
    }

    #[Test]
    public function influencer_settings_requires_city(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->set('city', '')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
            ->call('updateInfluencerSettings')
            ->assertHasErrors(['city']);
    }

    #[Test]
    public function influencer_settings_requires_state(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->set('city', 'Springfield')
            ->set('state', '')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
            ->call('updateInfluencerSettings')
            ->assertHasErrors(['state']);
    }

    #[Test]
    public function influencer_settings_requires_postal_code(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '')
            ->set('phone_number', '555-123-4567')
            ->call('updateInfluencerSettings')
            ->assertHasErrors(['postal_code']);
    }

    #[Test]
    public function influencer_settings_requires_phone_number(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '')
            ->call('updateInfluencerSettings')
            ->assertHasErrors(['phone_number']);
    }

    #[Test]
    public function influencer_settings_requires_typical_lead_time_days(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->call('setActiveTab', 'match')
            ->set('typical_lead_time_days', null)
            ->call('updateInfluencerSettings')
            ->assertHasErrors(['typical_lead_time_days']);
    }

    #[Test]
    public function influencer_can_update_social_accounts(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->call('setActiveTab', 'social')
            ->set('social_accounts.instagram.username', 'myinstahandle')
            ->set('social_accounts.instagram.followers', 10000)
            ->set('social_accounts.tiktok.username', 'mytiktokhandle')
            ->set('social_accounts.tiktok.followers', 50000)
            ->call('updateInfluencerSettings')
            ->assertHasNoErrors();

        $user->refresh();
        $influencer = $user->influencer;

        $instagramAccount = $influencer->socialAccounts()->where('platform', 'instagram')->first();
        $tiktokAccount = $influencer->socialAccounts()->where('platform', 'tiktok')->first();

        $this->assertNotNull($instagramAccount);
        $this->assertEquals('myinstahandle', $instagramAccount->username);
        $this->assertEquals(10000, $instagramAccount->followers);

        $this->assertNotNull($tiktokAccount);
        $this->assertEquals('mytiktokhandle', $tiktokAccount->username);
        $this->assertEquals(50000, $tiktokAccount->followers);
    }

    #[Test]
    public function influencer_username_must_be_unique(): void
    {
        $existingUser = User::factory()->influencer()->withProfile()->create();
        $existingUser->influencer->update(['username' => 'takenusername']);

        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        // Setting username with live validation triggers updatedUsername automatically
        Livewire::test(InfluencerSettings::class)
            ->set('username', 'takenusername')
            ->assertHasErrors(['username']);
    }

    #[Test]
    public function influencer_can_use_their_existing_username(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $user->influencer->update(['username' => 'myusername']);

        $this->actingAs($user);

        // Setting the same username should not cause validation errors
        Livewire::test(InfluencerSettings::class)
            ->assertSet('username', 'myusername')
            ->set('username', 'myusername')
            ->assertHasNoErrors(['username']);
    }

    #[Test]
    public function influencer_settings_uses_primary_industry_not_primary_niche(): void
    {
        // This test ensures the bug fix persists - the column is primary_industry, not primary_niche
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        // Test that the component properly sets and saves primary_industry on the match tab
        Livewire::test(InfluencerSettings::class)
            ->call('setActiveTab', 'match')
            ->set('primary_industry', BusinessIndustry::RETAIL->value)
            ->set('typical_lead_time_days', 7)
            ->call('updateInfluencerSettings')
            ->assertHasNoErrors();

        $user->refresh();

        // Verify the primary_industry column is properly set
        $this->assertEquals(BusinessIndustry::RETAIL, $user->influencer->primary_industry);
    }

    #[Test]
    public function influencer_can_view_promotion_section(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertStatus(200)
            ->assertSee('Promote Profile');
    }

    #[Test]
    public function influencer_can_promote_profile_with_credits(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $user->influencer->update(['promotion_credits' => 3]);

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertSet('promotion_credits', 3)
            ->assertSet('is_promoted', false)
            ->call('promoteProfile')
            ->assertSet('is_promoted', true)
            ->assertSet('promotion_credits', 2);

        $user->refresh();
        $this->assertTrue($user->influencer->is_promoted);
        $this->assertEquals(2, $user->influencer->promotion_credits);
        $this->assertNotNull($user->influencer->promoted_until);
    }

    #[Test]
    public function influencer_cannot_promote_profile_without_credits(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $user->influencer->update(['promotion_credits' => 0]);

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertSet('promotion_credits', 0)
            ->call('promoteProfile')
            ->assertSet('is_promoted', false);

        $user->refresh();
        $this->assertFalse($user->influencer->is_promoted);
    }

    #[Test]
    public function influencer_promotion_lasts_seven_days(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $user->influencer->update(['promotion_credits' => 1]);

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->call('promoteProfile');

        $user->refresh();
        $expectedEndDate = now()->addDays(7)->startOfDay();
        $actualEndDate = $user->influencer->promoted_until->startOfDay();

        $this->assertEquals($expectedEndDate->toDateString(), $actualEndDate->toDateString());
    }

    #[Test]
    public function influencer_sees_promotion_credits_count(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $user->influencer->update(['promotion_credits' => 5]);

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertSet('promotion_credits', 5)
            ->assertSee('5 credits available');
    }

    #[Test]
    public function influencer_sees_buy_credits_button_when_no_credits(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $user->influencer->update(['promotion_credits' => 0]);

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertSee('Buy Promotion Credits');
    }

    #[Test]
    public function influencer_sees_currently_promoted_status(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $user->influencer->update([
            'is_promoted' => true,
            'promoted_until' => now()->addDays(5),
        ]);

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertSet('is_promoted', true)
            ->assertSee('Currently Promoted until');
    }

    #[Test]
    public function promo_credit_price_computed_property_returns_active_price(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $product = StripeProduct::factory()->create();
        $price = StripePrice::factory()->oneTime()->forProduct($product)->create([
            'lookup_key' => 'profile_promo_credit_current',
            'active' => true,
            'unit_amount' => 999,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(InfluencerSettings::class);
        $promoCreditPrice = $component->get('promoCreditPrice');
        $this->assertEquals($price->id, $promoCreditPrice->id);
        $this->assertEquals(999, $promoCreditPrice->unit_amount);
    }

    #[Test]
    public function promo_credit_price_returns_null_when_no_active_price(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $product = StripeProduct::factory()->create();
        StripePrice::factory()->oneTime()->forProduct($product)->create([
            'lookup_key' => 'profile_promo_credit_current',
            'active' => false,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(InfluencerSettings::class);
        $this->assertNull($component->get('promoCreditPrice'));
    }

    #[Test]
    public function influencer_can_select_credit_quantity(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->assertSet('creditQuantity', 1)
            ->set('creditQuantity', 5)
            ->assertSet('creditQuantity', 5);
    }

    #[Test]
    public function purchase_credits_fails_with_invalid_quantity(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $product = StripeProduct::factory()->create();
        StripePrice::factory()->oneTime()->forProduct($product)->create([
            'lookup_key' => 'profile_promo_credit_current',
            'active' => true,
            'unit_amount' => 999,
        ]);

        $this->actingAs($user);

        // Test quantity too low
        Livewire::test(InfluencerSettings::class)
            ->set('creditQuantity', 0)
            ->call('purchasePromotionCredits');

        $user->refresh();
        $this->assertEquals(0, $user->influencer->promotion_credits);

        // Test quantity too high
        Livewire::test(InfluencerSettings::class)
            ->set('creditQuantity', 11)
            ->call('purchasePromotionCredits');

        $user->refresh();
        $this->assertEquals(0, $user->influencer->promotion_credits);
    }

    #[Test]
    public function purchase_credits_fails_when_price_not_available(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->set('creditQuantity', 1)
            ->call('purchasePromotionCredits');

        $user->refresh();
        $this->assertEquals(0, $user->influencer->promotion_credits);
    }
}
