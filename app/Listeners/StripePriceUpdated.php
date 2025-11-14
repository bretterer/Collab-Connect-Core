<?php

namespace App\Listeners;

use App\Models\StripePrice;
use Laravel\Cashier\Events\WebhookReceived;

class StripePriceUpdated
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'price.updated') {

            $priceData = $event->payload['data']['object'];

            // Update the existing StripePrice record in the database
            StripePrice::updateOrCreate([
                'stripe_id' => $priceData['id'],
            ], [
                'active' => $priceData['active'],
                'billing_scheme' => $priceData['billing_scheme'],
                'livemode' => $priceData['livemode'],
                'metadata' => $priceData['metadata'] ?? null,
                'recurring' => $priceData['recurring'] ?? null,
                'type' => $priceData['type'],
                'unit_amount' => $priceData['unit_amount'],
            ]);

        }
    }
}
