<?php

namespace App\Listeners;

use App\Models\StripePrice;
use Laravel\Cashier\Events\WebhookReceived;

class StripePriceDeleted
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'price.deleted') {

            $priceData = $event->payload['data']['object'];

            // Delete the StripePrice record from the database
            StripePrice::where('stripe_id', $priceData['id'])->delete();

        }
    }
}
