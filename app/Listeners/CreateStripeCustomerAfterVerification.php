<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;

class CreateStripeCustomerAfterVerification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        try {
            if (! $event->user->hasStripeId()) {
                $event->user->createOrGetStripeCustomer();
            }
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            Log::error('Failed to create Stripe customer after email verification: '.$e->getMessage(),
                [
                    'user_id' => $event->user->id,
                    'user_email' => $event->user->email,
                ],
            );
        }
    }
}
