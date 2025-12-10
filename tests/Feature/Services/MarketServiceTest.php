<?php

namespace Tests\Feature\Services;

use App\Models\Market;
use App\Models\MarketWaitlist;
use App\Models\MarketZipcode;
use App\Models\PostalCode;
use App\Models\User;
use App\Notifications\MarketOpenedNotification;
use App\Services\MarketService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketServiceTest extends TestCase
{
    use RefreshDatabase;

    private MarketService $marketService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->marketService = new MarketService;
    }

    #[Test]
    public function it_approves_waitlisted_users_for_postal_codes(): void
    {
        Event::fake([Registered::class]);
        Notification::fake();

        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();

        $user = User::factory()->create([
            'postal_code' => '12345',
            'market_approved' => false,
            'email_verified_at' => null,
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $result = $this->marketService->approveWaitlistedUsersForPostalCodes(['12345']);

        $this->assertEquals(1, $result['approved_count']);
        $this->assertTrue($user->fresh()->market_approved);
        $this->assertDatabaseMissing('market_waitlist', ['user_id' => $user->id]);

        Event::assertDispatched(Registered::class);
    }

    #[Test]
    public function it_does_not_approve_already_approved_users(): void
    {
        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();

        $user = User::factory()->create([
            'postal_code' => '12345',
            'market_approved' => true,
        ]);

        $result = $this->marketService->approveWaitlistedUsersForPostalCodes(['12345']);

        $this->assertEquals(0, $result['approved_count']);
    }

    #[Test]
    public function it_sends_notifications_when_enabled(): void
    {
        Notification::fake();
        Event::fake([Registered::class]);

        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();
        $market = Market::factory()->active()->create();

        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        $user = User::factory()->create([
            'postal_code' => '12345',
            'market_approved' => false,
            'email_verified_at' => now(),
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $this->marketService->approveWaitlistedUsersForPostalCodes(['12345'], $market, true);

        Notification::assertSentTo($user, MarketOpenedNotification::class);
    }

    #[Test]
    public function it_does_not_send_notifications_when_disabled(): void
    {
        Notification::fake();
        Event::fake([Registered::class]);

        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();
        $market = Market::factory()->active()->create();

        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        $user = User::factory()->create([
            'postal_code' => '12345',
            'market_approved' => false,
            'email_verified_at' => now(),
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $this->marketService->approveWaitlistedUsersForPostalCodes(['12345'], $market, false);

        Notification::assertNotSentTo($user, MarketOpenedNotification::class);
    }

    #[Test]
    public function it_approves_users_from_multiple_postal_codes(): void
    {
        Event::fake([Registered::class]);

        $postalCode1 = PostalCode::factory()->withPostalCode('11111')->create();
        $postalCode2 = PostalCode::factory()->withPostalCode('22222')->create();

        $user1 = User::factory()->create([
            'postal_code' => '11111',
            'market_approved' => false,
        ]);

        $user2 = User::factory()->create([
            'postal_code' => '22222',
            'market_approved' => false,
        ]);

        MarketWaitlist::create(['user_id' => $user1->id, 'postal_code' => '11111']);
        MarketWaitlist::create(['user_id' => $user2->id, 'postal_code' => '22222']);

        $result = $this->marketService->approveWaitlistedUsersForPostalCodes(['11111', '22222']);

        $this->assertEquals(2, $result['approved_count']);
        $this->assertTrue($user1->fresh()->market_approved);
        $this->assertTrue($user2->fresh()->market_approved);
    }

    #[Test]
    public function it_approves_waitlisted_users_for_market(): void
    {
        Event::fake([Registered::class]);

        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();
        $market = Market::factory()->active()->create();

        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        $user = User::factory()->create([
            'postal_code' => '12345',
            'market_approved' => false,
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $result = $this->marketService->approveWaitlistedUsersForMarket($market);

        $this->assertEquals(1, $result['approved_count']);
        $this->assertTrue($user->fresh()->market_approved);
    }

    #[Test]
    public function it_does_not_approve_users_for_inactive_market(): void
    {
        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();
        $market = Market::factory()->inactive()->create();

        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        $user = User::factory()->create([
            'postal_code' => '12345',
            'market_approved' => false,
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $result = $this->marketService->approveWaitlistedUsersForMarket($market);

        $this->assertEquals(0, $result['approved_count']);
        $this->assertFalse($user->fresh()->market_approved);
        $this->assertDatabaseHas('market_waitlist', ['user_id' => $user->id]);
    }

    #[Test]
    public function it_does_not_trigger_verification_for_already_verified_users(): void
    {
        Event::fake([Registered::class]);

        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();

        $user = User::factory()->create([
            'postal_code' => '12345',
            'market_approved' => false,
            'email_verified_at' => now(),
        ]);

        MarketWaitlist::create([
            'user_id' => $user->id,
            'postal_code' => '12345',
        ]);

        $this->marketService->approveWaitlistedUsersForPostalCodes(['12345']);

        Event::assertNotDispatched(Registered::class);
    }
}
