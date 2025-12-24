<?php

namespace App\Listeners\Stripe;

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

            // Initialize all subscription credits (renewing and one-time grants)
            // Delay to ensure subscription record exists in database first
            InitializeSubscriptionCredits::dispatch($subscriptionData)->delay(now()->addSeconds(10));
        }
    }
}
