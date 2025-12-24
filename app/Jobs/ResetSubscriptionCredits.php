<?php

namespace App\Jobs;

use App\Models\StripePrice;
use App\Models\SubscriptionCredit;
use App\Services\CashierService;
use App\Subscription\SubscriptionMetadataSchema;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;

class ResetSubscriptionCredits implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * Checks all active subscriptions and resets credits if a new billing period has started.
     */
    public function handle(): void
    {
        // Get all active subscriptions
        $subscriptions = Subscription::where('stripe_status', 'active')->get();

        foreach ($subscriptions as $subscription) {
            $this->processSubscription($subscription);
        }
    }

    /**
     * Process a single subscription for credit reset.
     */
    private function processSubscription(Subscription $subscription): void
    {
        try {
            // Get the current period start from Stripe
            $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($subscription->stripe_id);
            $currentPeriodStart = Carbon::createFromTimestamp($stripeSubscription->current_period_start);

            // Get credits that need to be reset (reset_at is before current period start)
            $creditsToReset = SubscriptionCredit::forSubscription($subscription->id)
                ->where(function ($query) use ($currentPeriodStart) {
                    $query->whereNull('reset_at')
                        ->orWhere('reset_at', '<', $currentPeriodStart);
                })
                ->get();

            if ($creditsToReset->isEmpty()) {
                return;
            }

            // Get the price metadata for this subscription
            $stripePrice = StripePrice::where('stripe_id', $subscription->stripe_price)->first();
            $metadata = $stripePrice?->metadata ?? [];

            // Reset each credit
            foreach ($creditsToReset as $credit) {
                $this->resetCredit($credit, $metadata);
            }

            Log::info('Subscription credits reset', [
                'subscription_id' => $subscription->id,
                'credits_reset' => $creditsToReset->pluck('key')->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to reset subscription credits', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Reset a single credit to its subscription limit.
     * If the user has more credits than the limit (e.g., from admin grant), preserve the excess.
     */
    private function resetCredit(SubscriptionCredit $credit, array $metadata): void
    {
        $limit = (int) ($metadata[$credit->key] ?? 0);

        // Skip unlimited credits (-1) and zero limits
        if ($limit <= 0) {
            return;
        }

        // Preserve excess credits - only reset to limit if current value is less
        // If user has more than the limit (e.g., admin granted extra), keep their current value
        $currentValue = $credit->value;
        $newValue = max($currentValue, $limit);

        // Only update if actually resetting (current is less than limit) or updating reset_at timestamp
        if ($currentValue < $limit) {
            $credit->reset($limit);
        } else {
            // Just update the reset timestamp, keep the higher value
            $credit->update(['reset_at' => now()]);
        }
    }

    /**
     * Reset credits for a specific subscription (called from webhook).
     * Preserves excess credits - if user has more than the plan limit, keeps their current value.
     */
    public static function resetForSubscription(string $stripeSubscriptionId): void
    {
        $subscription = Subscription::where('stripe_id', $stripeSubscriptionId)->first();

        if (! $subscription) {
            return;
        }

        // Get the billable model
        $billable = CashierService::findBillable($subscription->stripe_id);

        if (! $billable) {
            return;
        }

        // Get the price metadata for this subscription
        $stripePrice = StripePrice::where('stripe_id', $subscription->stripe_price)->first();
        $metadata = $stripePrice?->metadata ?? [];

        // Reset all credit keys for this subscription
        foreach (SubscriptionMetadataSchema::getCreditKeys() as $key) {
            $limit = (int) ($metadata[$key] ?? 0);

            // Skip unlimited credits (-1) and zero limits
            if ($limit <= 0) {
                continue;
            }

            // Get existing credit record
            $existingCredit = SubscriptionCredit::forSubscription($subscription->id)
                ->forKey($key)
                ->first();

            // Determine the new value - preserve excess credits
            $currentValue = $existingCredit?->value ?? 0;
            $newValue = max($currentValue, $limit);

            SubscriptionCredit::updateOrCreate(
                [
                    'subscription_id' => $subscription->id,
                    'key' => $key,
                ],
                [
                    'value' => $newValue,
                    'reset_at' => now(),
                ]
            );
        }

        Log::info('Subscription credits reset via webhook', [
            'subscription_id' => $subscription->id,
            'stripe_subscription_id' => $stripeSubscriptionId,
        ]);
    }
}
