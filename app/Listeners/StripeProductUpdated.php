<?php

namespace App\Listeners;

use App\Models\StripeProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeProductUpdated
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'product.updated') {

            $productData = $event->payload['data']['object'];

            // Update the existing StripeProduct record in the database
            StripeProduct::updateOrCreate([
                'stripe_id' => $productData['id'],
            ], [
                'name' => $productData['name'],
                'active' => $productData['active'],
                'description' => $productData['description'] ?? null,
                'metadata' => $productData['metadata'] ?? null,
                'livemode' => $productData['livemode'] ?? false,
            ]);

        }
    }
}

