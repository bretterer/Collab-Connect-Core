<?php

namespace App\Listeners;

use App\Models\StripePrice;
use App\Models\StripeProduct;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripePriceCreated
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        Log::info('StripePriceCreated event received', [
            'payload' => $event->payload,
        ]);
        if ($event->payload['type'] === 'price.created') {


            $priceData = $event->payload['data']['object'];

            $product = StripeProduct::query()
                ->where('stripe_id', $priceData['product'])
                ->first();

            if (!$product) {
                throw new \Exception('Stripe product not found');
            }

            // Create a new StripePrice record in the database
            StripePrice::create([
                'stripe_id' => $priceData['id'],
                'active' => $priceData['active'],
                'billing_scheme' => $priceData['billing_scheme'],
                'livemode' => $priceData['livemode'],
                'metadata' => $priceData['metadata'] ?? null,
                'stripe_product_id' => $product->id,
                'recurring' => $priceData['recurring'] ?? null,
                'type' => $priceData['type'],
                'unit_amount' => $priceData['unit_amount'],
            ]);
        }
    }
}
