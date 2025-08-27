<?php

namespace App\Listeners;

use App\Models\StripeProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeProductDeleted
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'product.deleted') {

            $productData = $event->payload['data']['object'];

            // Create a new StripeProduct record in the database
            (StripeProduct::where('stripe_id', $productData['id']))?->delete();

        }
    }
}
