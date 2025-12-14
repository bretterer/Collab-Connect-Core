<?php

namespace Tests\Feature\Feature\Livewire;

use App\Livewire\Onboarding\BusinessOnboarding;
use App\Models\Business;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscriptionPricingDisplayTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Business $business;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->business()->create();
        $this->business = Business::factory()->create();
        $this->user->setCurrentBusiness($this->business);
    }

    #[Test]
    public function it_displays_active_business_subscription_products()
    {
        // Create a business product with prices
        $product = StripeProduct::factory()->create([
            'name' => 'Business Premium',
            'active' => true,
            'billable_type' => 'App\Models\Business',
            'description' => 'Premium business plan',
        ]);

        StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'unit_amount' => 9900,
            'active' => true,
            'recurring' => ['interval' => 'month', 'interval_count' => 1],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(BusinessOnboarding::class)
            ->set('step', 5);

        $component
            ->assertSee('Business Premium')
            ->assertSee('Premium business plan')
            ->assertSee('$99.00')
            ->assertSee('/ month');
    }

    #[Test]
    public function it_hides_inactive_products()
    {
        // Create an inactive product
        $inactiveProduct = StripeProduct::factory()->create([
            'name' => 'Inactive Plan',
            'active' => false,
            'billable_type' => 'App\Models\Business',
        ]);

        StripePrice::factory()->create([
            'stripe_product_id' => $inactiveProduct->id,
            'unit_amount' => 5000,
            'active' => true,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(BusinessOnboarding::class)
            ->set('step', 5);

        $component->assertDontSee('Inactive Plan');
    }

    #[Test]
    public function it_hides_influencer_products_from_business_view()
    {
        // Create an influencer product
        $influencerProduct = StripeProduct::factory()->create([
            'name' => 'Influencer Plan',
            'active' => true,
            'billable_type' => 'App\Models\Influencer',
        ]);

        StripePrice::factory()->create([
            'stripe_product_id' => $influencerProduct->id,
            'unit_amount' => 4900,
            'active' => true,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(BusinessOnboarding::class)
            ->set('step', 5);

        $component->assertDontSee('Influencer Plan');
    }

    #[Test]
    public function it_displays_multiple_prices_for_a_product()
    {
        $product = StripeProduct::factory()->create([
            'name' => 'Business Plan',
            'active' => true,
            'billable_type' => 'App\Models\Business',
        ]);

        StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'unit_amount' => 9900,
            'active' => true,
            'recurring' => ['interval' => 'month', 'interval_count' => 1],
        ]);

        StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'unit_amount' => 99000,
            'active' => true,
            'recurring' => ['interval' => 'year', 'interval_count' => 1],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(BusinessOnboarding::class)
            ->set('step', 5);

        $component
            ->assertSee('$99.00')
            ->assertSee('$990.00')
            ->assertSee('/ month')
            ->assertSee('/ year');
    }

    #[Test]
    public function it_can_select_a_price()
    {
        $product = StripeProduct::factory()->create([
            'name' => 'Business Plan',
            'active' => true,
            'billable_type' => 'App\Models\Business',
        ]);

        $price = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'unit_amount' => 9900,
            'active' => true,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(BusinessOnboarding::class)
            ->set('step', 5);

        $component
            ->call('selectPrice', $price->id)
            ->assertSet('selectedPriceId', $price->id);
    }

    #[Test]
    public function it_shows_selected_badge_when_price_is_selected()
    {
        $product = StripeProduct::factory()->create([
            'name' => 'Business Plan',
            'active' => true,
            'billable_type' => 'App\Models\Business',
        ]);

        $price = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'unit_amount' => 9900,
            'active' => true,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(BusinessOnboarding::class)
            ->set('step', 5)
            ->call('selectPrice', $price->id);

        $component->assertSee('Selected');
    }

    #[Test]
    public function it_shows_message_when_no_products_available()
    {
        $component = Livewire::actingAs($this->user)
            ->test(BusinessOnboarding::class)
            ->set('step', 5);

        $component->assertSee('No subscription plans are currently available');
    }
}
