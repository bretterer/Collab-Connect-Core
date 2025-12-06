<?php

namespace Tests\Feature\Livewire\Influencer;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompensationType;
use App\Livewire\Influencer\InfluencerSettings;
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
            ->assertSee('Influencer Settings');
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

        Livewire::test(InfluencerSettings::class)
            ->set('username', 'testinfluencer')
            ->set('bio', 'This is my updated bio for testing purposes.')
            ->set('primary_industry', BusinessIndustry::FOOD_BEVERAGE->value)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
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
            BusinessIndustry::FOOD_BEVERAGE->value,
            BusinessIndustry::FITNESS_WELLNESS->value,
        ];

        Livewire::test(InfluencerSettings::class)
            ->set('content_types', $contentTypes)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
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
            ->set('preferred_business_types', $businessTypes)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
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
            ->set('compensation_types', $compensationTypes)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
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
            ->set('typical_lead_time_days', 7)
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
            ->set('typical_lead_time_days', 7)
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
            ->set('typical_lead_time_days', 7)
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
            ->set('typical_lead_time_days', 7)
            ->call('updateInfluencerSettings')
            ->assertHasErrors(['phone_number']);
    }

    #[Test]
    public function influencer_settings_requires_typical_lead_time_days(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
            ->set('typical_lead_time_days', null)
            ->call('updateInfluencerSettings')
            ->assertHasErrors(['typical_lead_time_days']);
    }

    #[Test]
    public function influencer_can_add_and_remove_content_types(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        // Set up influencer with a single content type to test from a known state
        $user->influencer->update(['content_types' => [BusinessIndustry::FOOD_BEVERAGE->value]]);

        $this->actingAs($user);

        $component = Livewire::test(InfluencerSettings::class);

        // Start with one content type
        $this->assertCount(1, $component->get('content_types'));

        // Add a content type
        $component->call('addContentType');
        $this->assertCount(2, $component->get('content_types'));

        // Add another content type (should be limited to 3)
        $component->call('addContentType');
        $this->assertCount(3, $component->get('content_types'));

        // Try to add a fourth - should not exceed 3
        $component->call('addContentType');
        $this->assertCount(3, $component->get('content_types'));

        // Remove a content type
        $component->call('removeContentType', 0);
        $this->assertCount(2, $component->get('content_types'));
    }

    #[Test]
    public function influencer_can_add_and_remove_business_types(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        // Set up influencer with a single business type to test from a known state
        $user->influencer->update(['preferred_business_types' => [BusinessType::RESTAURANT->value]]);

        $this->actingAs($user);

        $component = Livewire::test(InfluencerSettings::class);

        // Start with one business type
        $this->assertCount(1, $component->get('preferred_business_types'));

        // Add a business type
        $component->call('addBusinessType');
        $this->assertCount(2, $component->get('preferred_business_types'));

        // Try to add a third - should not exceed 2
        $component->call('addBusinessType');
        $this->assertCount(2, $component->get('preferred_business_types'));

        // Remove a business type
        $component->call('removeBusinessType', 0);
        $this->assertCount(1, $component->get('preferred_business_types'));
    }

    #[Test]
    public function influencer_can_add_and_remove_compensation_types(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        // Set up influencer with a single compensation type to test from a known state
        $user->influencer->update(['compensation_types' => [CompensationType::MONETARY->value]]);

        $this->actingAs($user);

        $component = Livewire::test(InfluencerSettings::class);

        // Start with one compensation type
        $this->assertCount(1, $component->get('compensation_types'));

        // Add a compensation type
        $component->call('addCompensationType');
        $this->assertCount(2, $component->get('compensation_types'));

        // Add another compensation type (should be limited to 3)
        $component->call('addCompensationType');
        $this->assertCount(3, $component->get('compensation_types'));

        // Try to add a fourth - should not exceed 3
        $component->call('addCompensationType');
        $this->assertCount(3, $component->get('compensation_types'));

        // Remove a compensation type
        $component->call('removeCompensationType', 0);
        $this->assertCount(2, $component->get('compensation_types'));
    }

    #[Test]
    public function influencer_can_update_social_accounts(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        Livewire::test(InfluencerSettings::class)
            ->set('social_accounts.instagram.username', 'myinstahandle')
            ->set('social_accounts.instagram.followers', 10000)
            ->set('social_accounts.tiktok.username', 'mytiktokhandle')
            ->set('social_accounts.tiktok.followers', 50000)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
            ->set('typical_lead_time_days', 7)
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

        // Test that the component properly sets and saves primary_industry
        Livewire::test(InfluencerSettings::class)
            ->set('primary_industry', BusinessIndustry::RETAIL->value)
            ->set('city', 'Springfield')
            ->set('state', 'OH')
            ->set('postal_code', '45066')
            ->set('phone_number', '555-123-4567')
            ->set('typical_lead_time_days', 7)
            ->call('updateInfluencerSettings')
            ->assertHasNoErrors();

        $user->refresh();

        // Verify the primary_industry column is properly set
        $this->assertEquals(BusinessIndustry::RETAIL, $user->influencer->primary_industry);
    }
}
