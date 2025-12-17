<?php

namespace App\Listeners\Stripe;

use App\Jobs\AddWelcomeProfilePromotionCredits;
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

            AddWelcomeProfilePromotionCredits::dispatch($subscriptionData)->delay(now()->addSeconds(10));
        }
    }
}
