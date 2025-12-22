<?php

namespace Tests\Feature\Livewire\LinkInBio;

use App\Livewire\LinkInBio\Sections\JoinReferral\Index;
use App\Models\Influencer;
use App\Models\ReferralEnrollment;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JoinReferralIndexTest extends TestCase
{
    use RefreshDatabase;

    private function createEliteInfluencer(): Influencer
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $product = StripeProduct::factory()->create([
            'billable_type' => Influencer::class,
            'name' => 'Influencer',
        ]);

        $stripePrice = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'influencer_elite',
            'active' => true,
        ]);

        $influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => $stripePrice->stripe_id,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return $influencer->fresh();
    }

    private function createProfessionalInfluencer(): Influencer
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $product = StripeProduct::factory()->create([
            'billable_type' => Influencer::class,
            'name' => 'Influencer',
        ]);

        $stripePrice = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'influencer_professional',
            'active' => true,
        ]);

        $influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => $stripePrice->stripe_id,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return $influencer->fresh();
    }

    private function enrollUserInReferralProgram(User $user): ReferralEnrollment
    {
        return ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'code' => 'TEST'.strtoupper(Str::random(6)),
        ]);
    }

    #[Test]
    public function it_renders_the_join_referral_section_for_elite_users_with_enrollment(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Join Referral');
    }

    #[Test]
    public function it_shows_section_with_paywall_for_non_elite_users(): void
    {
        $influencer = $this->createProfessionalInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Join Referral')
            ->assertSee('Elite Feature');
    }

    #[Test]
    public function it_hides_section_for_users_not_enrolled_in_referral_program(): void
    {
        $influencer = $this->createEliteInfluencer();

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertDontSee('Join Referral');
    }

    #[Test]
    public function it_loads_default_settings(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertSet('enabled', false)
            ->assertSet('text', 'Join CollabConnect')
            ->assertSet('style', 'secondary')
            ->assertSet('buttonColor', '#000000');
    }

    #[Test]
    public function it_loads_settings_from_mount(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        Livewire::test(Index::class, [
            'settings' => [
                'enabled' => true,
                'text' => 'Sign Up Now',
                'style' => 'outline',
            ],
        ])
            ->assertSet('enabled', true)
            ->assertSet('text', 'Sign Up Now')
            ->assertSet('style', 'outline');
    }

    #[Test]
    public function it_dispatches_section_updated_event_when_enabled_toggled(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->set('enabled', true)
            ->assertDispatched('section-updated', section: 'joinReferral');
    }

    #[Test]
    public function it_dispatches_section_updated_event_when_text_changed(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->set('text', 'New Button Text')
            ->assertDispatched('section-updated', section: 'joinReferral');
    }

    #[Test]
    public function it_dispatches_section_updated_event_when_style_changed(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->set('style', 'primary')
            ->assertDispatched('section-updated', section: 'joinReferral');
    }

    #[Test]
    public function it_returns_correct_section_key(): void
    {
        $this->assertEquals('joinReferral', Index::sectionKey());
    }

    #[Test]
    public function it_returns_correct_default_settings(): void
    {
        $defaults = Index::defaultSettings();

        $this->assertEquals([
            'enabled' => false,
            'text' => 'Join CollabConnect',
            'style' => 'secondary',
            'buttonColor' => '#000000',
        ], $defaults);
    }

    #[Test]
    public function it_generates_correct_referral_url(): void
    {
        $influencer = $this->createEliteInfluencer();
        $enrollment = $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class);

        $this->assertStringContainsString('/r/'.$enrollment->code, $component->get('referralUrl'));
    }

    #[Test]
    public function it_returns_empty_referral_url_when_not_enrolled(): void
    {
        $influencer = $this->createEliteInfluencer();

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class);

        $this->assertEquals('', $component->get('referralUrl'));
    }

    #[Test]
    public function to_settings_array_returns_correct_format(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class, [
            'settings' => [
                'enabled' => true,
                'text' => 'Custom Text',
                'style' => 'outline',
                'buttonColor' => '#dc2626',
            ],
        ]);

        $settings = $component->instance()->toSettingsArray();

        $this->assertEquals([
            'enabled' => true,
            'text' => 'Custom Text',
            'style' => 'outline',
            'buttonColor' => '#dc2626',
        ], $settings);
    }

    #[Test]
    public function it_dispatches_section_updated_event_when_button_color_changed(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->set('buttonColor', '#dc2626')
            ->assertDispatched('section-updated', section: 'joinReferral');
    }

    #[Test]
    public function can_show_section_returns_true_for_elite_users_with_enrollment(): void
    {
        $influencer = $this->createEliteInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class);

        $this->assertTrue($component->get('canShowSection'));
    }

    #[Test]
    public function can_show_section_returns_true_for_non_elite_users_with_enrollment(): void
    {
        $influencer = $this->createProfessionalInfluencer();
        $this->enrollUserInReferralProgram($influencer->user);

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class);

        // Section shows for enrolled users (Elite access handled via paywall)
        $this->assertTrue($component->get('canShowSection'));
        $this->assertFalse($component->get('hasEliteAccess'));
    }

    #[Test]
    public function can_show_section_returns_false_for_users_without_enrollment(): void
    {
        $influencer = $this->createEliteInfluencer();

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class);

        $this->assertFalse($component->get('canShowSection'));
    }
}
