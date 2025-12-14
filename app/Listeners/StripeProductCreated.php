<?php

namespace App\Listeners;

use App\Models\StripeProduct;
use Laravel\Cashier\Events\WebhookReceived;

class StripeProductCreated
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'product.created') {

            $productData = $event->payload['data']['object'];

            // Create a new StripeProduct record in the database
            StripeProduct::create([
                'stripe_id' => $productData['id'],
                'name' => $productData['name'],
                'active' => $productData['active'],
                'description' => $productData['description'] ?? null,
                'metadata' => $productData['metadata'] ?? null,
                'billable_type' => $productData['metadata']['billable_type'] ?? null,
                'livemode' => $productData['livemode'] ?? false,
            ]);

        }
    }
}
