<?php

namespace App\Http\Controllers;

use App\Services\CashierService;
use Laravel\Cashier\Http\Controllers\WebhookController;

class CashierWebhookController extends WebhookController
{
    /**
     * Get the customer instance by Stripe ID.
     *
     * @param  string|null  $stripeId
     * @return \Laravel\Cashier\Billable|null
     */
    protected function getUserByStripeId($stripeId)
    {
        return CashierService::findBillable($stripeId);
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        if ($payload['api_version'] == '2025-06-30.basil') {
            if (count($payload['data']['object']['items']['data']) == 1) {
                $payload['data']['object']['current_period_end'] = $payload['data']['object']['items']['data'][0]['current_period_end'];
            }
        }

        parent::handleCustomerSubscriptionUpdated($payload);

    }
}
