<?php

namespace App\Listeners\Stripe;

use App\Jobs\AddWelcomeProfilePromotionCredits;
use App\Jobs\InitializeSubscriptionCredits;
use Laravel\Cashier\Events\WebhookReceived;

class SubscriptionCreated
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'customer.subscription.created') {
            $subscriptionData = $event->payload['data']['object'];

            // Initialize subscription credits in the subscription_credits table
            // Delay to ensure subscription record exists in database first
            InitializeSubscriptionCredits::dispatch($subscriptionData)->delay(now()->addSeconds(10));

            // Also update legacy promotion_credits on the billable model
            // This can be removed once all code uses the new SubscriptionCredit system
            AddWelcomeProfilePromotionCredits::dispatch($subscriptionData)->delay(now()->addSeconds(10));
        }
    }
}
