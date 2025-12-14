<?php

namespace App\Listeners;

use App\Models\StripePrice;
use App\Models\StripeProduct;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\Stripe;

class StripePriceUpdated
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'price.updated') {

            $priceData = $event->payload['data']['object'];

            // Get the associated StripeProduct
            $product = StripeProduct::query()
                ->where('stripe_id', $priceData['product'])
                ->first();

            if (! $product) {
                // See if the product exists at Stripe
                $stripeProduct = app('stripe')->products->retrieve($priceData['product']);
                if ($stripeProduct) {
                    // Create the product locally
                    $product = StripeProduct::create([
                        'stripe_id' => $stripeProduct->id,
                        'name' => $stripeProduct->name,
                        'description' => $stripeProduct->description,
                        'metadata' => $stripeProduct->metadata ?? null,
                        'billable_type' => $stripeProduct->metadata['billable_type'] ?? null,
                        'livemode' => $stripeProduct->livemode ?? false,
                    ]);
                } else {
                    throw new \Exception('Stripe product not found: '.$priceData['product']);
                }
            }

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
                'stripe_product_id' => $product->id,
                'product_name' => $priceData['nickname'] ?? null,
                'currency' => $priceData['currency'] ?? null,
                'lookup_key' => $priceData['lookup_key'] ?? null,
            ]);

        }
    }
}
