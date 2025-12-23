<?php

namespace App\Listeners;

use App\Jobs\ResetSubscriptionCredits;
use Laravel\Cashier\Events\WebhookReceived;

class ResetCreditsOnInvoicePayment
{
    /**
     * Handle the event.
     *
     * Resets subscription credits when an invoice is paid for a subscription cycle renewal.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'invoice.payment_succeeded') {
            return;
        }

        $invoiceData = $event->payload['data']['object'];

        // Only process subscription cycle renewals (not initial subscription)
        // billing_reason can be: subscription_create, subscription_cycle, subscription_update, manual, upcoming
        if (($invoiceData['billing_reason'] ?? null) !== 'subscription_cycle') {
            return;
        }

        $subscriptionId = $invoiceData['subscription'] ?? null;

        if (! $subscriptionId) {
            return;
        }

        // Reset credits for this subscription
        ResetSubscriptionCredits::resetForSubscription($subscriptionId);
    }
}
