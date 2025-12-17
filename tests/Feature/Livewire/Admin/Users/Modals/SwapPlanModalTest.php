<?php

namespace Tests\Feature\Livewire\Admin\Users\Modals;

use App\Enums\AccountType;
use App\Livewire\Admin\Users\Modals\SwapPlanModal;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SwapPlanModalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);
    }

    private function createBusinessUserWithSubscription(): User
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        $priceId = 'price_test_'.Str::random(14);
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => $priceId,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return $user;
    }

    private function createInfluencerUserWithSubscription(): User
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $priceId = 'price_test_'.Str::random(14);
        $influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => $priceId,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return $user;
    }

    private function createAvailablePlansForBusiness(): void
    {
        $product = StripeProduct::factory()->create([
            'billable_type' => Business::class,
            'name' => 'Business Plan',
            'active' => true,
        ]);

        StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'business_monthly',
            'active' => true,
            'unit_amount' => 2900,
        ]);

        StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'business_yearly',
            'active' => true,
            'unit_amount' => 29000,
        ]);
    }

    private function createAvailablePlansForInfluencer(): void
    {
        $product = StripeProduct::factory()->create([
            'billable_type' => Influencer::class,
            'name' => 'Influencer Plan',
            'active' => true,
        ]);

        StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'influencer_professional',
            'active' => true,
            'unit_amount' => 1900,
        ]);

        StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'influencer_elite',
            'active' => true,
            'unit_amount' => 3900,
        ]);
    }

    #[Test]
    public function it_can_render_the_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_can_open_modal_for_business_user(): void
    {
        $user = $this->createBusinessUserWithSubscription();

        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('selectedPlan', '')
            ->assertSet('swapTiming', 'immediately');
    }

    #[Test]
    public function it_can_open_modal_for_influencer_user(): void
    {
        $user = $this->createInfluencerUserWithSubscription();

        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('selectedPlan', '')
            ->assertSet('swapTiming', 'immediately');
    }

    #[Test]
    public function it_computes_user_correctly(): void
    {
        $user = $this->createBusinessUserWithSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id);

        $this->assertEquals($user->id, $component->get('user')->id);
    }

    #[Test]
    public function it_computes_billable_for_business_user(): void
    {
        $user = $this->createBusinessUserWithSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Business::class, $billable);
    }

    #[Test]
    public function it_computes_billable_for_influencer_user(): void
    {
        $user = $this->createInfluencerUserWithSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Influencer::class, $billable);
    }

    #[Test]
    public function it_computes_current_price_id(): void
    {
        $user = $this->createBusinessUserWithSubscription();
        $expectedPriceId = $user->currentBusiness->subscription('default')->stripe_price;

        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id);

        $this->assertEquals($expectedPriceId, $component->get('currentPriceId'));
    }

    #[Test]
    public function it_computes_available_plans_for_business(): void
    {
        $this->createAvailablePlansForBusiness();
        $user = $this->createBusinessUserWithSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id);

        $availablePlans = $component->get('availablePlans');
        $this->assertCount(1, $availablePlans);
        $this->assertEquals('Business Plan', $availablePlans->first()->name);
    }

    #[Test]
    public function it_computes_available_plans_for_influencer(): void
    {
        $this->createAvailablePlansForInfluencer();
        $user = $this->createInfluencerUserWithSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id);

        $availablePlans = $component->get('availablePlans');
        $this->assertCount(1, $availablePlans);
        $this->assertEquals('Influencer Plan', $availablePlans->first()->name);
    }

    #[Test]
    public function it_validates_selected_plan_required(): void
    {
        $user = $this->createBusinessUserWithSubscription();

        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id)
            ->set('selectedPlan', '')
            ->call('swapPlan')
            ->assertHasErrors(['selectedPlan' => 'required']);
    }

    #[Test]
    public function it_validates_swap_timing(): void
    {
        $user = $this->createBusinessUserWithSubscription();

        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id)
            ->set('selectedPlan', 'price_new_plan')
            ->set('swapTiming', 'invalid_timing')
            ->call('swapPlan')
            ->assertHasErrors(['swapTiming' => 'in']);
    }

    #[Test]
    public function it_fails_when_selecting_same_plan(): void
    {
        $user = $this->createBusinessUserWithSubscription();
        $currentPriceId = $user->currentBusiness->subscription('default')->stripe_price;

        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id)
            ->set('selectedPlan', $currentPriceId)
            ->call('swapPlan')
            ->assertNotDispatched('subscription-updated');
    }

    #[Test]
    public function it_fails_when_no_subscription_exists(): void
    {
        // Create user without subscription
        $user = User::factory()->business()->withProfile()->create();

        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->call('open', $user->id)
            ->set('selectedPlan', 'price_new_plan')
            ->call('swapPlan')
            ->assertNotDispatched('subscription-updated');
    }

    #[Test]
    public function it_returns_null_for_user_without_user_id(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class);

        $this->assertNull($component->get('user'));
    }

    #[Test]
    public function it_returns_null_for_billable_without_user(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class);

        $this->assertNull($component->get('billable'));
    }

    #[Test]
    public function it_returns_empty_collection_for_available_plans_without_billable(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class);

        $this->assertCount(0, $component->get('availablePlans'));
    }

    #[Test]
    public function it_resets_selected_plan_on_open(): void
    {
        $user = $this->createBusinessUserWithSubscription();

        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->set('selectedPlan', 'price_old')
            ->call('open', $user->id)
            ->assertSet('selectedPlan', '');
    }

    #[Test]
    public function it_resets_swap_timing_to_immediately_on_open(): void
    {
        $user = $this->createBusinessUserWithSubscription();

        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->set('swapTiming', 'end_of_period')
            ->call('open', $user->id)
            ->assertSet('swapTiming', 'immediately');
    }

    #[Test]
    public function it_sets_is_processing_to_false_initially(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SwapPlanModal::class)
            ->assertSet('isProcessing', false);
    }
}
