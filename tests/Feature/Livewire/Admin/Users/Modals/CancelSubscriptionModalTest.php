<?php

namespace Tests\Feature\Livewire\Admin\Users\Modals;

use App\Enums\AccountType;
use App\Livewire\Admin\Users\Modals\CancelSubscriptionModal;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CancelSubscriptionModalTest extends TestCase
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

    private function createBusinessUserWithActiveSubscription(): User
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_'.Str::random(14),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return $user;
    }

    private function createInfluencerUserWithActiveSubscription(): User
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_'.Str::random(14),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return $user;
    }

    #[Test]
    public function it_can_render_the_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_can_open_modal_for_business_user(): void
    {
        $user = $this->createBusinessUserWithActiveSubscription();

        Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id);
    }

    #[Test]
    public function it_can_open_modal_for_influencer_user(): void
    {
        $user = $this->createInfluencerUserWithActiveSubscription();

        Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id);
    }

    #[Test]
    public function it_computes_user_correctly(): void
    {
        $user = $this->createBusinessUserWithActiveSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class)
            ->call('open', $user->id);

        $this->assertEquals($user->id, $component->get('user')->id);
    }

    #[Test]
    public function it_computes_billable_for_business_user(): void
    {
        $user = $this->createBusinessUserWithActiveSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Business::class, $billable);
    }

    #[Test]
    public function it_computes_billable_for_influencer_user(): void
    {
        $user = $this->createInfluencerUserWithActiveSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Influencer::class, $billable);
    }

    #[Test]
    public function it_fails_when_no_subscription_exists_for_cancel(): void
    {
        // Create user without subscription
        $user = User::factory()->business()->withProfile()->create();

        Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class)
            ->call('open', $user->id)
            ->call('cancelSubscription')
            ->assertNotDispatched('subscription-updated');
    }

    #[Test]
    public function it_fails_when_no_subscription_exists_for_cancel_immediately(): void
    {
        // Create user without subscription
        $user = User::factory()->business()->withProfile()->create();

        Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class)
            ->call('open', $user->id)
            ->call('cancelSubscriptionImmediately')
            ->assertNotDispatched('subscription-updated');
    }

    #[Test]
    public function it_returns_null_for_user_without_user_id(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class);

        $this->assertNull($component->get('user'));
    }

    #[Test]
    public function it_returns_null_for_billable_without_user(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class);

        $this->assertNull($component->get('billable'));
    }

    #[Test]
    public function it_sets_processing_state_to_false_initially(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(CancelSubscriptionModal::class);

        $this->assertFalse($component->get('isProcessing'));
    }
}
