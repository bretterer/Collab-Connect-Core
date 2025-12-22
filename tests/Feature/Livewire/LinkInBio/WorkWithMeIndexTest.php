<?php

namespace Tests\Feature\Livewire\LinkInBio;

use App\Livewire\LinkInBio\Sections\WorkWithMe\Index;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkWithMeIndexTest extends TestCase
{
    use RefreshDatabase;

    private function createSubscribedInfluencer(string $lookupKey): Influencer
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $product = StripeProduct::factory()->create([
            'billable_type' => Influencer::class,
            'name' => 'Influencer',
        ]);

        $stripePrice = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => $lookupKey,
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

    #[Test]
    public function it_renders_the_work_with_me_section(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Work With Me');
    }

    #[Test]
    public function it_loads_default_settings(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertSet('enabled', true)
            ->assertSet('text', 'Work With Me')
            ->assertSet('style', 'secondary')
            ->assertSet('buttonColor', '#000000');
    }

    #[Test]
    public function it_loads_settings_from_mount(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class, [
            'settings' => [
                'enabled' => false,
                'text' => 'Custom Button Text',
                'style' => 'outline',
            ],
        ])
            ->assertSet('enabled', false)
            ->assertSet('text', 'Custom Button Text')
            ->assertSet('style', 'outline');
    }

    #[Test]
    public function it_dispatches_section_updated_event_when_enabled_toggled(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->set('enabled', false)
            ->assertDispatched('section-updated', section: 'workWithMe');
    }

    #[Test]
    public function it_dispatches_section_updated_event_when_text_changed(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->set('text', 'New Button Text')
            ->assertDispatched('section-updated', section: 'workWithMe');
    }

    #[Test]
    public function it_dispatches_section_updated_event_when_style_changed(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->set('style', 'secondary')
            ->assertDispatched('section-updated', section: 'workWithMe');
    }

    #[Test]
    public function it_returns_correct_section_key(): void
    {
        $this->assertEquals('workWithMe', Index::sectionKey());
    }

    #[Test]
    public function it_returns_correct_default_settings(): void
    {
        $defaults = Index::defaultSettings();

        $this->assertEquals([
            'enabled' => true,
            'text' => 'Work With Me',
            'style' => 'secondary',
            'buttonColor' => '#000000',
        ], $defaults);
    }

    #[Test]
    public function it_generates_correct_profile_url_with_username(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;
        $influencer->username = 'testusername';
        $influencer->save();

        $this->actingAs($user);

        $component = Livewire::test(Index::class);

        $this->assertStringContainsString('testusername', $component->get('profileUrl'));
    }

    #[Test]
    public function it_generates_profile_url_with_user_id_fallback(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;
        $influencer->username = null;
        $influencer->save();

        $this->actingAs($user);

        $component = Livewire::test(Index::class);

        $this->assertStringContainsString((string) $influencer->user_id, $component->get('profileUrl'));
    }

    #[Test]
    public function it_shows_searchable_status_correctly(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        // Initially not searchable
        $influencer->is_searchable = false;
        $influencer->save();

        $this->actingAs($user);

        $component = Livewire::test(Index::class);
        $this->assertFalse($component->get('isProfileSearchable'));

        // Now make searchable
        $influencer->is_searchable = true;
        $influencer->save();

        $component = Livewire::test(Index::class);
        $this->assertTrue($component->get('isProfileSearchable'));
    }

    #[Test]
    public function to_settings_array_returns_correct_format(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class, [
            'settings' => [
                'enabled' => false,
                'text' => 'Custom Text',
                'style' => 'outline',
                'buttonColor' => '#dc2626',
            ],
        ]);

        $settings = $component->instance()->toSettingsArray();

        $this->assertEquals([
            'enabled' => false,
            'text' => 'Custom Text',
            'style' => 'outline',
            'buttonColor' => '#dc2626',
        ], $settings);
    }

    #[Test]
    public function it_dispatches_section_updated_event_when_button_color_changed(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->set('buttonColor', '#dc2626')
            ->assertDispatched('section-updated', section: 'workWithMe');
    }
}
