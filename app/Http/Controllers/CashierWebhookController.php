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
}
