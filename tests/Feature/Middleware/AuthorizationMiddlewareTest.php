<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use App\Settings\RegistrationMarkets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthorizationMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // ==================== EnsureAdminAccess Tests ====================

    #[Test]
    public function unauthenticated_user_redirected_to_login_for_admin_routes(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function non_admin_user_gets_403_for_admin_routes(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $this->actingAs($businessUser);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    #[Test]
    public function influencer_gets_403_for_admin_routes(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($influencer);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_access_admin_users_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/users');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_access_admin_markets_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/markets');

        $response->assertStatus(200);
    }

    // ==================== EnsureOnboardingCompleted Tests ====================

    #[Test]
    public function business_user_without_profile_redirected_to_onboarding(): void
    {
        // Create business user without profile (onboarding incomplete)
        $user = User::factory()->business()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('onboarding.business'));
    }

    #[Test]
    public function business_user_with_incomplete_onboarding_redirected(): void
    {
        $user = User::factory()->business()->create();

        // Create business but mark onboarding as incomplete
        $business = \App\Models\Business::factory()->create([
            'onboarding_complete' => false,
        ]);

        \App\Models\BusinessUser::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $user->setCurrentBusiness($business);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('onboarding.business'));
    }

    #[Test]
    public function business_user_with_completed_onboarding_can_access_dashboard(): void
    {
        // Disable market registration for this test
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        // Also set market_approved to ensure no market redirects
        $user = User::factory()->business()->withProfile()->create([
            'market_approved' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard/business');

        $response->assertStatus(200);
    }

    #[Test]
    public function influencer_without_profile_redirected_to_onboarding(): void
    {
        // Create influencer user without profile (onboarding incomplete)
        $user = User::factory()->influencer()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('onboarding.influencer'));
    }

    #[Test]
    public function influencer_with_incomplete_onboarding_redirected(): void
    {
        $user = User::factory()->influencer()->create();

        // Create influencer profile but mark onboarding as incomplete
        \App\Models\Influencer::factory()->create([
            'user_id' => $user->id,
            'onboarding_complete' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('onboarding.influencer'));
    }

    #[Test]
    public function influencer_with_completed_onboarding_can_access_dashboard(): void
    {
        // Disable market registration for this test
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard/influencer');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_bypasses_onboarding_check(): void
    {
        // Disable market registration so we only test onboarding bypass
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        // Admin should be able to access admin dashboard without any profile/onboarding
        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    // ==================== EnsureMarketApproved Tests ====================

    #[Test]
    public function market_approval_not_required_when_markets_disabled(): void
    {
        // Disable market registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = false;
        $settings->save();

        $user = User::factory()->business()->withProfile()->create([
            'market_approved' => false,
            'postal_code' => '12345',
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard/business');

        // Should be allowed through even without market approval
        $response->assertStatus(200);
    }

    #[Test]
    public function non_market_approved_user_redirected_to_waitlist(): void
    {
        // Enable market registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        $user = User::factory()->business()->withProfile()->create([
            'market_approved' => false,
            'postal_code' => '12345',
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('market-waitlist'));
    }

    #[Test]
    public function market_approved_user_can_access_dashboard(): void
    {
        // Enable market registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        // User must have both market_approved AND completed onboarding
        $user = User::factory()->business()->withProfile()->create([
            'market_approved' => true,
            'postal_code' => '12345',
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard/business');

        $response->assertStatus(200);
    }

    #[Test]
    public function legacy_user_without_postal_code_bypasses_market_check(): void
    {
        // Enable market registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        // User has completed onboarding but no postal code (legacy user)
        $user = User::factory()->business()->withProfile()->create([
            'market_approved' => false,
            'postal_code' => null, // Legacy user without postal code
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard/business');

        // Legacy users bypass market approval
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_bypasses_market_approval_check(): void
    {
        // Enable market registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        // Admin doesn't need profile/onboarding - just test market bypass
        $admin = User::factory()->admin()->create([
            'market_approved' => false,
            'postal_code' => '12345',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');

        // Admin should bypass market approval
        $response->assertStatus(200);
    }

    #[Test]
    public function waitlisted_user_can_access_waitlist_page(): void
    {
        // Enable market registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        $user = User::factory()->business()->withProfile()->create([
            'market_approved' => false,
            'postal_code' => '12345',
        ]);

        $this->actingAs($user);

        // Waitlist page should be accessible even without market approval
        $response = $this->get('/market-waitlist');

        $response->assertStatus(200);
    }

    // ==================== Middleware Chain Tests ====================

    #[Test]
    public function middleware_chain_redirects_correctly_for_incomplete_business(): void
    {
        // Enable market registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        // Create user with market approval but incomplete onboarding
        $user = User::factory()->business()->create([
            'market_approved' => true,
            'postal_code' => '12345',
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        // Should redirect to onboarding (after passing market check)
        $response->assertRedirect(route('onboarding.business'));
    }

    #[Test]
    public function fully_setup_business_user_can_access_all_authenticated_routes(): void
    {
        // Enable market registration
        $settings = app(RegistrationMarkets::class);
        $settings->enabled = true;
        $settings->save();

        // Create fully setup user: business type, profile complete, market approved
        $user = User::factory()->business()->withProfile()->subscribed()->create([
            'market_approved' => true,
            'postal_code' => '12345',
        ]);

        $this->actingAs($user);

        // Dashboard - should be accessible
        $this->get('/dashboard/business')->assertStatus(200);

        // Campaigns - should be accessible
        $this->get('/campaigns')->assertStatus(200);

        // Search - should be accessible
        $this->get('/search')->assertStatus(200);
    }
}
