<?php

namespace Tests\Feature\Livewire\LinkInBio;

use App\Livewire\LinkInBio\Sections\RatesCard\Index;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RatesCardIndexTest extends TestCase
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
    public function it_renders_the_rates_card_section(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Rates Card');
    }

    #[Test]
    public function elite_user_can_add_rate(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertSet('items', [])
            ->call('addRate')
            ->assertCount('items', 1);
    }

    #[Test]
    public function elite_user_can_remove_rate(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['platform' => 'Instagram', 'rate' => '$100', 'description' => 'Post', 'enabled' => true],
                ],
            ],
        ])
            ->assertCount('items', 1)
            ->call('removeRate', 0)
            ->assertCount('items', 0);
    }

    #[Test]
    public function professional_user_does_not_have_customization_access(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertSet('hasCustomizationAccess', false);
    }

    #[Test]
    public function elite_user_has_customization_access(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertSet('hasCustomizationAccess', true);
    }

    #[Test]
    public function dispatches_section_updated_event_when_rate_added(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->call('addRate')
            ->assertDispatched('section-updated');
    }

    #[Test]
    public function dispatches_section_updated_event_when_rate_removed(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['platform' => 'Instagram', 'rate' => '$100', 'description' => 'Post', 'enabled' => true],
                ],
            ],
        ])
            ->call('removeRate', 0)
            ->assertDispatched('section-updated');
    }

    #[Test]
    public function loads_default_settings_correctly(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertSet('enabled', true)
            ->assertSet('title', 'My Rates')
            ->assertSet('subtitle', '')
            ->assertSet('size', 'small')
            ->assertSet('items', []);
    }

    #[Test]
    public function loads_custom_settings_correctly(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class, [
            'settings' => [
                'enabled' => false,
                'title' => 'Custom Title',
                'subtitle' => 'Custom Subtitle',
                'size' => 'large',
                'items' => [
                    ['platform' => 'TikTok', 'rate' => '$500', 'description' => 'Video', 'enabled' => true],
                ],
            ],
        ])
            ->assertSet('enabled', false)
            ->assertSet('title', 'Custom Title')
            ->assertSet('subtitle', 'Custom Subtitle')
            ->assertSet('size', 'large')
            ->assertCount('items', 1);
    }

    #[Test]
    public function returns_correct_settings_array(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class, [
            'settings' => [
                'title' => 'Test Title',
                'subtitle' => 'Test Subtitle',
            ],
        ]);

        $settings = $component->instance()->toSettingsArray();

        $this->assertEquals('Test Title', $settings['title']);
        $this->assertEquals('Test Subtitle', $settings['subtitle']);
        $this->assertTrue($settings['enabled']);
    }
}
