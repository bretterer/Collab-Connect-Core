<?php

namespace Tests\Feature\Auth;

use App\Events\UserRegisteredWithReferral;
use App\Livewire\Auth\Register;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        if (! config('collabconnect.registration_enabled')) {
            $response->assertStatus(404);

            return;
        } elseif (config('collabconnect.beta_registration_only')) {
            $response->assertStatus(403);

            return;
        } else {
            $response->assertStatus(200);
        }
    }

    #[Test]
    public function new_users_can_register(): void
    {
        $this->withoutExceptionHandling();

        // Disable Honeypot
        config(['honeypot.enabled' => false]);

        $response = Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register');

        $response
            ->assertHasNoErrors();

        $this->assertAuthenticated();
    }

    #[Test]
    public function a_referred_user_triggers_referral_link_recording_event_after_registering()
    {
        Event::fake();

        // Disable Honeypot
        config(['honeypot.enabled' => false]);

        // Create referrer and their enrollment
        $referrerEnrollment = \App\Models\ReferralEnrollment::factory()->create();

        // Simulate visiting registration with referral code cookie
        $response = Livewire::withCookies([
            'referral_code' => $referrerEnrollment->code,
        ])->test(Register::class)
            ->set('name', 'Referred User')
            ->set('email', 'referred@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register');

        $response
            ->assertHasNoErrors();

        $this->assertAuthenticated();

        Event::assertDispatched(UserRegisteredWithReferral::class);

    }
}
