<?php

namespace Tests\Feature\Auth;

use App\Enums\AccountType;
use App\Livewire\Auth\Register;
use App\Models\Market;
use App\Models\MarketZipcode;
use App\Models\PostalCode;
use App\Models\User;
use App\Settings\RegistrationMarkets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketBasedRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable honeypot for tests
        config(['honeypot.enabled' => false]);
    }

    #[Test]
    public function user_can_register_with_postal_code_in_active_market(): void
    {
        // Enable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        // Create a postal code and market
        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();
        $market = Market::factory()->active()->create();
        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        $response = Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('postal_code', '12345')
            ->set('accountType', AccountType::INFLUENCER)
            ->call('register');

        $response->assertHasNoErrors();

        // Verify user was created and is market approved
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->market_approved);
        $this->assertEquals('12345', $user->postal_code);

        // Verify user is NOT on waitlist
        $this->assertDatabaseMissing('market_waitlist', [
            'user_id' => $user->id,
        ]);

        // Verify user is authenticated
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_registration_fails_with_invalid_postal_code_when_markets_enabled(): void
    {
        // Enable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        $response = Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('postal_code', '99999')
            ->set('accountType', AccountType::INFLUENCER)
            ->call('register');

        $response->assertHasErrors(['postal_code']);

        // Verify user was NOT created
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function user_registration_requires_postal_code_when_markets_enabled(): void
    {
        // Enable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        $response = Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('postal_code', '')
            ->set('accountType', AccountType::INFLUENCER)
            ->call('register');

        $response->assertHasErrors(['postal_code']);
    }

    #[Test]
    public function user_with_postal_code_outside_active_market_is_waitlisted(): void
    {
        // Enable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        // Create a postal code but NO active market for it
        $postalCode = PostalCode::factory()->withPostalCode('54321')->create();

        $response = Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('postal_code', '54321')
            ->set('accountType', AccountType::INFLUENCER)
            ->call('register');

        $response->assertHasNoErrors();

        // Verify user was created but NOT market approved
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->market_approved);
        $this->assertEquals('54321', $user->postal_code);

        // Verify user IS on waitlist
        $this->assertDatabaseHas('market_waitlist', [
            'user_id' => $user->id,
            'postal_code' => '54321',
        ]);

        // Verify user is authenticated
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_can_register_without_postal_code_when_markets_disabled(): void
    {
        // Disable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        $response = Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('postal_code', '')
            ->set('accountType', AccountType::INFLUENCER)
            ->call('register');

        $response->assertHasNoErrors();

        // Verify user was created and is market approved
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->market_approved);
        $this->assertEmpty($user->postal_code);

        // Verify user is NOT on waitlist
        $this->assertDatabaseMissing('market_waitlist', [
            'user_id' => $user->id,
        ]);

        // Verify user is authenticated
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_can_register_with_any_postal_code_when_markets_disabled(): void
    {
        // Disable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        // Create a postal code that is NOT in any market
        $postalCode = PostalCode::factory()->withPostalCode('99999')->create();

        $response = Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('postal_code', '99999')
            ->set('accountType', AccountType::INFLUENCER)
            ->call('register');

        $response->assertHasNoErrors();

        // Verify user was created and is market approved
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->market_approved);
        $this->assertEquals('99999', $user->postal_code);

        // Verify user is NOT on waitlist
        $this->assertDatabaseMissing('market_waitlist', [
            'user_id' => $user->id,
        ]);

        // Verify user is authenticated
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function postal_code_field_visibility_reflects_market_settings(): void
    {
        // When markets are enabled
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        $response = Livewire::test(Register::class);
        $this->assertTrue($response->get('registrationMarketsEnabled'));

        // When markets are disabled
        $settings->enabled = false;
        $settings->save();

        $response = Livewire::test(Register::class);
        $this->assertFalse($response->get('registrationMarketsEnabled'));
    }

    #[Test]
    public function user_in_inactive_market_is_waitlisted(): void
    {
        // Enable market-based registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        // Create a postal code and INACTIVE market
        $postalCode = PostalCode::factory()->withPostalCode('11111')->create();
        $market = Market::factory()->inactive()->create();
        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '11111',
        ]);

        $response = Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('postal_code', '11111')
            ->set('accountType', AccountType::INFLUENCER)
            ->call('register');

        $response->assertHasNoErrors();

        // Verify user was created but NOT market approved
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->market_approved);

        // Verify user IS on waitlist
        $this->assertDatabaseHas('market_waitlist', [
            'user_id' => $user->id,
            'postal_code' => '11111',
        ]);
    }
}
