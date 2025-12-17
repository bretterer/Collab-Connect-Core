<?php

namespace Tests\Feature\Livewire\Admin\Users\Modals;

use App\Enums\AccountType;
use App\Livewire\Admin\Users\Modals\CancelTrialModal;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CancelTrialModalTest extends TestCase
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

    private function createBusinessUserWithTrialSubscription(): User
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'trialing',
            'stripe_price' => 'price_test_'.Str::random(14),
            'quantity' => 1,
            'trial_ends_at' => now()->addDays(7),
            'ends_at' => null,
        ]);

        return $user;
    }

    private function createInfluencerUserWithTrialSubscription(): User
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(14),
            'stripe_status' => 'trialing',
            'stripe_price' => 'price_test_'.Str::random(14),
            'quantity' => 1,
            'trial_ends_at' => now()->addDays(7),
            'ends_at' => null,
        ]);

        return $user;
    }

    #[Test]
    public function it_can_render_the_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_can_open_modal_for_business_user(): void
    {
        $user = $this->createBusinessUserWithTrialSubscription();

        Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('cancelTrialAction', 'no_subscription');
    }

    #[Test]
    public function it_can_open_modal_for_influencer_user(): void
    {
        $user = $this->createInfluencerUserWithTrialSubscription();

        Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('cancelTrialAction', 'no_subscription');
    }

    #[Test]
    public function it_computes_user_correctly(): void
    {
        $user = $this->createBusinessUserWithTrialSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->call('open', $user->id);

        $this->assertEquals($user->id, $component->get('user')->id);
    }

    #[Test]
    public function it_computes_billable_for_business_user(): void
    {
        $user = $this->createBusinessUserWithTrialSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Business::class, $billable);
    }

    #[Test]
    public function it_computes_billable_for_influencer_user(): void
    {
        $user = $this->createInfluencerUserWithTrialSubscription();

        $component = Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Influencer::class, $billable);
    }

    #[Test]
    public function it_fails_when_no_subscription_exists(): void
    {
        // Create user without subscription
        $user = User::factory()->business()->withProfile()->create();

        Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->call('open', $user->id)
            ->call('cancelTrial')
            ->assertNotDispatched('subscription-updated');
    }

    #[Test]
    public function it_returns_null_for_user_without_user_id(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class);

        $this->assertNull($component->get('user'));
    }

    #[Test]
    public function it_returns_null_for_billable_without_user(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class);

        $this->assertNull($component->get('billable'));
    }

    #[Test]
    public function it_resets_cancel_trial_action_on_open(): void
    {
        $user = $this->createBusinessUserWithTrialSubscription();

        Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->set('cancelTrialAction', 'convert_to_paid')
            ->call('open', $user->id)
            ->assertSet('cancelTrialAction', 'no_subscription');
    }

    #[Test]
    public function it_sets_is_processing_to_false_initially(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->assertSet('isProcessing', false);
    }

    #[Test]
    public function it_can_set_cancel_trial_action_to_convert_to_paid(): void
    {
        $user = $this->createBusinessUserWithTrialSubscription();

        Livewire::actingAs($this->admin)
            ->test(CancelTrialModal::class)
            ->call('open', $user->id)
            ->set('cancelTrialAction', 'convert_to_paid')
            ->assertSet('cancelTrialAction', 'convert_to_paid');
    }
}
