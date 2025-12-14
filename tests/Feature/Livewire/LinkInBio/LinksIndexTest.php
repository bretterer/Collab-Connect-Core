<?php

namespace Tests\Feature\Livewire\LinkInBio;

use App\Livewire\LinkInBio\Sections\Links\Index;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LinksIndexTest extends TestCase
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
    public function it_renders_the_links_section(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Links');
    }

    #[Test]
    public function professional_user_can_add_links_up_to_limit(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class)
            ->assertSet('items', []);

        // Add first link
        $component->call('addLink')
            ->assertCount('items', 1);

        // Add second link
        $component->call('addLink')
            ->assertCount('items', 2);

        // Add third link (at limit)
        $component->call('addLink')
            ->assertCount('items', 3);
    }

    #[Test]
    public function professional_user_cannot_exceed_link_limit(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['title' => 'Link 1', 'url' => 'https://example1.com', 'icon' => null, 'enabled' => true],
                    ['title' => 'Link 2', 'url' => 'https://example2.com', 'icon' => null, 'enabled' => true],
                    ['title' => 'Link 3', 'url' => 'https://example3.com', 'icon' => null, 'enabled' => true],
                ],
            ],
        ]);

        // Verify we have 3 links
        $component->assertCount('items', 3);

        // Try to add a fourth link - should be blocked
        $component->call('addLink')
            ->assertCount('items', 3); // Still 3, not 4
    }

    #[Test]
    public function elite_user_can_add_unlimited_links(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['title' => 'Link 1', 'url' => 'https://example1.com', 'icon' => null, 'enabled' => true],
                    ['title' => 'Link 2', 'url' => 'https://example2.com', 'icon' => null, 'enabled' => true],
                    ['title' => 'Link 3', 'url' => 'https://example3.com', 'icon' => null, 'enabled' => true],
                ],
            ],
        ]);

        // Elite user can add beyond 3 links
        $component->call('addLink')
            ->assertCount('items', 4);

        $component->call('addLink')
            ->assertCount('items', 5);
    }

    #[Test]
    public function user_can_remove_a_link(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['title' => 'Link 1', 'url' => 'https://example1.com', 'icon' => null, 'enabled' => true],
                    ['title' => 'Link 2', 'url' => 'https://example2.com', 'icon' => null, 'enabled' => true],
                ],
            ],
        ]);

        $component->assertCount('items', 2);

        $component->call('removeLink', 0)
            ->assertCount('items', 1);
    }

    #[Test]
    public function link_limit_computed_property_returns_correct_value_for_professional(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertSet('linkLimit', 3);
    }

    #[Test]
    public function link_limit_computed_property_returns_max_int_for_elite(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_elite');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->assertSet('linkLimit', PHP_INT_MAX);
    }

    #[Test]
    public function can_add_more_links_returns_true_when_under_limit(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['title' => 'Link 1', 'url' => 'https://example1.com', 'icon' => null, 'enabled' => true],
                ],
            ],
        ])
            ->assertSet('canAddMoreLinks', true);
    }

    #[Test]
    public function can_add_more_links_returns_false_when_at_limit(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['title' => 'Link 1', 'url' => 'https://example1.com', 'icon' => null, 'enabled' => true],
                    ['title' => 'Link 2', 'url' => 'https://example2.com', 'icon' => null, 'enabled' => true],
                    ['title' => 'Link 3', 'url' => 'https://example3.com', 'icon' => null, 'enabled' => true],
                ],
            ],
        ])
            ->assertSet('canAddMoreLinks', false);
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
    public function user_can_edit_a_link(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['title' => 'Original Title', 'url' => 'https://original.com', 'icon' => null, 'enabled' => true],
                ],
            ],
        ]);

        // Open edit modal
        $component->call('editLink', 0)
            ->assertSet('editingLinkIndex', 0)
            ->assertSet('linkForm.title', 'Original Title')
            ->assertSet('linkForm.url', 'https://original.com');

        // Update the form
        $component->set('linkForm.title', 'Updated Title')
            ->set('linkForm.url', 'https://updated.com')
            ->call('saveLinkEdit');

        // Verify the link was updated
        $items = $component->get('items');
        $this->assertEquals('Updated Title', $items[0]['title']);
        $this->assertEquals('https://updated.com', $items[0]['url']);
    }

    #[Test]
    public function user_can_cancel_link_edit(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        $component = Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['title' => 'Original Title', 'url' => 'https://original.com', 'icon' => null, 'enabled' => true],
                ],
            ],
        ]);

        // Open edit modal and modify
        $component->call('editLink', 0)
            ->set('linkForm.title', 'Modified Title')
            ->call('cancelLinkEdit');

        // Verify modal was closed and original data preserved
        $component->assertSet('editingLinkIndex', null);

        $items = $component->get('items');
        $this->assertEquals('Original Title', $items[0]['title']);
    }

    #[Test]
    public function dispatches_section_updated_event_when_link_added(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class)
            ->call('addLink')
            ->assertDispatched('section-updated');
    }

    #[Test]
    public function dispatches_section_updated_event_when_link_removed(): void
    {
        $influencer = $this->createSubscribedInfluencer('influencer_professional');

        $this->actingAs($influencer->user);

        Livewire::test(Index::class, [
            'settings' => [
                'items' => [
                    ['title' => 'Link 1', 'url' => 'https://example.com', 'icon' => null, 'enabled' => true],
                ],
            ],
        ])
            ->call('removeLink', 0)
            ->assertDispatched('section-updated');
    }
}
