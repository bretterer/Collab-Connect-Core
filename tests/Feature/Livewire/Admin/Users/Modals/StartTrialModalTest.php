<?php

namespace Tests\Feature\Livewire\Admin\Users\Modals;

use App\Enums\AccountType;
use App\Livewire\Admin\Users\Modals\StartTrialModal;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StartTrialModalTest extends TestCase
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

    private function createBusinessUser(): User
    {
        return User::factory()->business()->withProfile()->create();
    }

    private function createInfluencerUser(): User
    {
        return User::factory()->influencer()->withProfile()->create();
    }

    private function createAvailablePlansForBusiness(): StripePrice
    {
        $product = StripeProduct::factory()->create([
            'billable_type' => Business::class,
            'name' => 'Business Plan',
            'active' => true,
        ]);

        return StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'business_monthly',
            'active' => true,
            'unit_amount' => 2900,
        ]);
    }

    private function createAvailablePlansForInfluencer(): StripePrice
    {
        $product = StripeProduct::factory()->create([
            'billable_type' => Influencer::class,
            'name' => 'Influencer Plan',
            'active' => true,
        ]);

        return StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'influencer_professional',
            'active' => true,
            'unit_amount' => 1900,
        ]);
    }

    #[Test]
    public function it_can_render_the_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_initializes_with_default_trial_days(): void
    {
        Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->assertSet('trialDays', 14);
    }

    #[Test]
    public function it_can_open_modal_for_business_user(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('selectedPlan', '');
    }

    #[Test]
    public function it_can_open_modal_for_influencer_user(): void
    {
        $user = $this->createInfluencerUser();

        Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('selectedPlan', '');
    }

    #[Test]
    public function it_computes_user_correctly(): void
    {
        $user = $this->createBusinessUser();

        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id);

        $this->assertEquals($user->id, $component->get('user')->id);
    }

    #[Test]
    public function it_computes_billable_for_business_user(): void
    {
        $user = $this->createBusinessUser();

        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Business::class, $billable);
    }

    #[Test]
    public function it_computes_billable_for_influencer_user(): void
    {
        $user = $this->createInfluencerUser();

        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Influencer::class, $billable);
    }

    #[Test]
    public function it_computes_available_plans_for_business(): void
    {
        $this->createAvailablePlansForBusiness();
        $user = $this->createBusinessUser();

        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id);

        $availablePlans = $component->get('availablePlans');
        $this->assertCount(1, $availablePlans);
        $this->assertEquals('Business Plan', $availablePlans->first()->name);
    }

    #[Test]
    public function it_computes_available_plans_for_influencer(): void
    {
        $this->createAvailablePlansForInfluencer();
        $user = $this->createInfluencerUser();

        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id);

        $availablePlans = $component->get('availablePlans');
        $this->assertCount(1, $availablePlans);
        $this->assertEquals('Influencer Plan', $availablePlans->first()->name);
    }

    #[Test]
    public function it_validates_selected_plan_required(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id)
            ->set('selectedPlan', '')
            ->call('startTrial')
            ->assertHasErrors(['selectedPlan' => 'required']);
    }

    #[Test]
    public function it_validates_trial_days_minimum(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id)
            ->set('selectedPlan', 'price_test')
            ->set('trialDays', 0)
            ->call('startTrial')
            ->assertHasErrors(['trialDays' => 'min']);
    }

    #[Test]
    public function it_validates_trial_days_maximum(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id)
            ->set('selectedPlan', 'price_test')
            ->set('trialDays', 100)
            ->call('startTrial')
            ->assertHasErrors(['trialDays' => 'max']);
    }

    #[Test]
    public function it_fails_when_no_billable_exists(): void
    {
        // Create user without a profile
        $user = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id)
            ->set('selectedPlan', 'price_test')
            ->set('trialDays', 14)
            ->call('startTrial')
            ->assertNotDispatched('subscription-updated');
    }

    #[Test]
    public function it_returns_null_for_user_without_user_id(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class);

        $this->assertNull($component->get('user'));
    }

    #[Test]
    public function it_returns_null_for_billable_without_user(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class);

        $this->assertNull($component->get('billable'));
    }

    #[Test]
    public function it_returns_empty_collection_for_available_plans_without_billable(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class);

        $this->assertCount(0, $component->get('availablePlans'));
    }

    #[Test]
    public function it_only_shows_active_plans(): void
    {
        $user = $this->createBusinessUser();

        $product = StripeProduct::factory()->create([
            'billable_type' => Business::class,
            'name' => 'Business Plan',
            'active' => true,
        ]);

        // Create an active plan
        StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'business_monthly',
            'active' => true,
        ]);

        // Create an inactive plan
        StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'business_yearly',
            'active' => false,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id);

        $availablePlans = $component->get('availablePlans');
        $this->assertCount(1, $availablePlans);
        $prices = $availablePlans->first()->prices;
        $this->assertCount(1, $prices);
    }

    #[Test]
    public function it_only_shows_recurring_plans(): void
    {
        $user = $this->createBusinessUser();

        $product = StripeProduct::factory()->create([
            'billable_type' => Business::class,
            'name' => 'Business Plan',
            'active' => true,
        ]);

        // Create a recurring plan
        StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'business_monthly',
            'active' => true,
        ]);

        // Create a one-time plan
        StripePrice::factory()->oneTime()->create([
            'stripe_product_id' => $product->id,
            'lookup_key' => 'business_setup',
            'active' => true,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(StartTrialModal::class)
            ->call('open', $user->id);

        $availablePlans = $component->get('availablePlans');
        $prices = $availablePlans->first()->prices;
        $this->assertCount(1, $prices);
    }
}
