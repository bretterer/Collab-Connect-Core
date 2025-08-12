<?php

namespace App\Listeners;

use App\Models\StripeProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
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
                'livemode' => $productData['livemode'] ?? false,
            ]);

        }
    }
}
