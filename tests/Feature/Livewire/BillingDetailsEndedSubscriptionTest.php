<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Profile\BillingDetails;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BillingDetailsEndedSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_ended_subscriptions_are_not_shown_as_current(): void
    {
        // Create a business user with a subscription
        $user = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $user->currentBusiness;

        // Get the subscription and cancel it with an end date in the past
        $subscription = $business->subscription('default');
        $subscription->ends_at = Carbon::now()->subDay();
        $subscription->save();

        $this->actingAs($user);

        // Test the billing details component
        $component = Livewire::test(BillingDetails::class);

        // The currentSubscription property should be null since the subscription has ended
        $this->assertNull($component->get('currentSubscription'));

        // The view should show "No Active Subscription"
        $component->assertSee('No Active Subscription');
    }

    public function test_canceled_but_not_ended_subscriptions_are_shown(): void
    {
        // Create a business user with a subscription
        $user = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $user->currentBusiness;

        // Cancel the subscription but set end date in the future
        $subscription = $business->subscription('default');
        $subscription->ends_at = Carbon::now()->addWeek();
        $subscription->save();

        $this->actingAs($user);

        // Test the billing details component
        $component = Livewire::test(BillingDetails::class);

        // The currentSubscription property should NOT be null since subscription hasn't ended yet
        $this->assertNotNull($component->get('currentSubscription'));

        // Should show the cancellation notice
        $component->assertSee('Cancels on');
    }

    public function test_ended_subscription_is_treated_as_no_subscription(): void
    {
        // Create a business user with a subscription
        $user = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $user->currentBusiness;

        // Cancel and end the subscription
        $subscription = $business->subscription('default');
        $subscription->ends_at = Carbon::now()->subDay();
        $subscription->save();

        $this->actingAs($user);

        // Verify the subscription is actually ended according to Cashier
        $this->assertTrue($business->fresh()->subscription('default')->ended());

        // The currentSubscription property should return null for ended subscriptions
        $component = Livewire::test(BillingDetails::class);
        $this->assertNull($component->get('currentSubscription'));

        // The page should show "No Active Subscription" just like a user with no subscription
        $component->assertSee('No Active Subscription');
        $component->assertSee('Choose a Plan');

        // Should NOT see Resume or Cancel buttons (since there's no current subscription)
        $component->assertDontSee('Resume');
    }

    public function test_canceled_but_not_ended_subscription_shows_resume_button(): void
    {
        // Create a business user with a subscription
        $user = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $user->currentBusiness;

        // Cancel but don't end the subscription yet
        $subscription = $business->subscription('default');
        $subscription->ends_at = Carbon::now()->addWeek();
        $subscription->save();

        $this->actingAs($user);

        // The component should show the Resume button and cancellation notice
        $component = Livewire::test(BillingDetails::class);

        // Should see the subscription details, Resume button, and cancellation notice
        $component->assertSee('Resume');
        $component->assertSee('Cancels on');
    }

    public function test_ended_subscription_allows_new_subscription(): void
    {
        // Create a business user with an ended subscription
        $user = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $user->currentBusiness;

        // End the subscription
        $subscription = $business->subscription('default');
        $subscription->ends_at = Carbon::now()->subDay();
        $subscription->save();

        $this->actingAs($user);

        // Test the billing details component shows option to create new subscription
        $component = Livewire::test(BillingDetails::class);

        // Should show "No Active Subscription" and "Choose a Plan" button
        $component->assertSee('No Active Subscription');
        $component->assertSee('Choose a Plan');
    }
}
