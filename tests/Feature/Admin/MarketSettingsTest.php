<?php

namespace Tests\Feature\Admin;

use App\Enums\AccountType;
use App\Livewire\Admin\Markets\MarketSettings;
use App\Models\User;
use App\Settings\RegistrationMarkets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketSettingsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_access_market_settings_page(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $response = $this->actingAs($admin)->get('/admin/markets/settings');

        $response->assertStatus(200);
        $response->assertSeeLivewire(MarketSettings::class);
    }

    #[Test]
    public function non_admin_cannot_access_market_settings_page(): void
    {
        $user = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $response = $this->actingAs($user)->get('/admin/markets/settings');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_toggle_market_registration_enabled_setting(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $settings = app(RegistrationMarkets::class);
        $this->assertTrue($settings->enabled);

        Livewire::actingAs($admin)
            ->test(MarketSettings::class)
            ->set('enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        // Verify setting was saved
        $settings->refresh();
        $this->assertFalse($settings->enabled);
    }

    #[Test]
    public function admin_can_toggle_market_registration_disabled_setting(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        // First disable it
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        Livewire::actingAs($admin)
            ->test(MarketSettings::class)
            ->assertSet('enabled', false)
            ->set('enabled', true)
            ->call('save')
            ->assertHasNoErrors();

        // Verify setting was saved
        $settings->refresh();
        $this->assertTrue($settings->enabled);
    }

    #[Test]
    public function market_settings_page_shows_current_setting_value(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        Livewire::actingAs($admin)
            ->test(MarketSettings::class)
            ->assertSet('enabled', false);
    }

    #[Test]
    public function market_settings_saves_and_persists_changes(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        Livewire::actingAs($admin)
            ->test(MarketSettings::class)
            ->assertSet('enabled', true)
            ->set('enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        // Verify the change persisted
        $settings->refresh();
        $this->assertFalse($settings->enabled);

        // Test it again from a fresh component instance
        Livewire::actingAs($admin)
            ->test(MarketSettings::class)
            ->assertSet('enabled', false);
    }
}
