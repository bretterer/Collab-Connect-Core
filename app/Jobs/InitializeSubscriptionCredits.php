<?php

namespace App\Jobs;

use App\Facades\SubscriptionLimits;
use App\Services\CashierService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Laravel\Cashier\Subscription;

class InitializeSubscriptionCredits implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param  array<string, mixed>  $subscriptionData
     */
    public function __construct(public array $subscriptionData) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscriptionId = $this->subscriptionData['id'];

        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();

        if (! $subscription) {
            return;
        }

        // Only initialize credits for active subscriptions
        if ($subscription->stripe_status !== 'active') {
            return;
        }

        // Get the billable model (Influencer or Business)
        $billable = CashierService::findBillable($subscription->stripe_id);

        if (! $billable) {
            return;
        }

        // Initialize all credits based on the subscription's price metadata
        // This creates SubscriptionCredit records in the database
        SubscriptionLimits::initializeCredits($billable);
    }
}
