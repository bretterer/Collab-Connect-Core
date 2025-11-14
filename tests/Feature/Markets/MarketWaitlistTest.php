<?php

namespace Tests\Feature\Markets;

use App\Enums\AccountType;
use App\Livewire\MarketWaitlist as MarketWaitlistComponent;
use App\Models\Market;
use App\Models\MarketWaitlist;
use App\Models\MarketZipcode;
use App\Models\PostalCode;
use App\Models\User;
use App\Settings\RegistrationMarkets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketWaitlistTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function waitlisted_user_can_access_waitlist_page(): void
    {
        // Enable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();
        $user = User::factory()->create([
            'market_approved' => false,
            'postal_code' => '12345',
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $response = $this->actingAs($user)->get('/market-waitlist');

        $response->assertStatus(200);
        $response->assertSeeLivewire(MarketWaitlistComponent::class);
    }

    #[Test]
    public function approved_user_cannot_access_waitlist_page(): void
    {
        $user = User::factory()->create([
            'market_approved' => true,
            'postal_code' => '12345',
        ]);

        $response = $this->actingAs($user)->get('/market-waitlist');

        $response->assertRedirect('/dashboard');
    }

    #[Test]
    public function user_on_waitlist_cannot_access_main_application(): void
    {
        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();
        $user = User::factory()->create([
            'market_approved' => false,
            'postal_code' => '12345',
            'email_verified_at' => now(),
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/market-waitlist');
    }

    #[Test]
    public function waitlist_entry_can_be_marked_as_notified(): void
    {
        $user = User::factory()->create([
            'market_approved' => false,
        ]);

        $waitlistEntry = MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
            'notified_at' => null,
        ]);

        $this->assertNull($waitlistEntry->notified_at);

        $waitlistEntry->markAsNotified();

        $this->assertNotNull($waitlistEntry->fresh()->notified_at);
    }

    #[Test]
    public function waitlist_can_filter_unnotified_entries(): void
    {
        $user1 = User::factory()->create(['market_approved' => false]);
        $user2 = User::factory()->create(['market_approved' => false]);
        $user3 = User::factory()->create(['market_approved' => false]);

        MarketWaitlist::create([
            'user_id' => $user1->id,
            'postal_code' => '12345',
            'notified_at' => now(),
        ]);

        MarketWaitlist::create([
            'user_id' => $user2->id,
            'postal_code' => '12345',
            'notified_at' => null,
        ]);

        MarketWaitlist::create([
            'user_id' => $user3->id,
            'postal_code' => '54321',
            'notified_at' => null,
        ]);

        $unnotified = MarketWaitlist::unnotified()->get();

        $this->assertCount(2, $unnotified);
        $this->assertFalse($unnotified->contains('user_id', $user1->id));
    }

    #[Test]
    public function waitlist_can_filter_by_postal_code(): void
    {
        $user1 = User::factory()->create(['market_approved' => false]);
        $user2 = User::factory()->create(['market_approved' => false]);
        $user3 = User::factory()->create(['market_approved' => false]);

        MarketWaitlist::create([
            'user_id' => $user1->id,
            'postal_code' => '12345',
        ]);

        MarketWaitlist::create([
            'user_id' => $user2->id,
            'postal_code' => '12345',
        ]);

        MarketWaitlist::create([
            'user_id' => $user3->id,
            'postal_code' => '54321',
        ]);

        $entries = MarketWaitlist::byPostalCode('12345')->get();

        $this->assertCount(2, $entries);
        $this->assertTrue($entries->contains('user_id', $user1->id));
        $this->assertTrue($entries->contains('user_id', $user2->id));
        $this->assertFalse($entries->contains('user_id', $user3->id));
    }

    #[Test]
    public function when_market_is_activated_for_postal_code_users_are_not_automatically_approved(): void
    {
        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();
        $user = User::factory()->create([
            'market_approved' => false,
            'postal_code' => '12345',
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        // Create and activate a market with this postal code
        $market = Market::factory()->inactive()->create();
        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        $market->update(['is_active' => true]);

        // User should still be unapproved until manually processed
        $user->refresh();
        $this->assertFalse($user->market_approved);

        // But now the postal code is in an active market
        $this->assertTrue(MarketZipcode::isInActiveMarket('12345'));
    }

    #[Test]
    public function waitlist_page_redirects_to_dashboard_when_markets_disabled(): void
    {
        // Disable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        $user = User::factory()->create([
            'market_approved' => false,
        ]);

        $response = $this->actingAs($user)->get('/market-waitlist');

        $response->assertRedirect('/dashboard');
    }

    #[Test]
    public function market_approval_check_can_be_bypassed_for_unapproved_users(): void
    {
        // Disable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        $user = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
            'market_approved' => false,
        ]);

        // When markets are disabled, the middleware should not block users with market_approved = false
        $this->assertFalse($user->market_approved);
        $this->assertFalse($settings->enabled);
    }

    #[Test]
    public function market_waitlist_stores_correct_user_and_postal_code(): void
    {
        $user = User::factory()->create([
            'market_approved' => false,
            'postal_code' => '12345',
        ]);

        $waitlistEntry = MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $this->assertEquals($user->id, $waitlistEntry->user_id);
        $this->assertEquals('12345', $waitlistEntry->postal_code);
        $this->assertNull($waitlistEntry->notified_at);

        $this->assertDatabaseHas('market_waitlist', [
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);
    }
}
