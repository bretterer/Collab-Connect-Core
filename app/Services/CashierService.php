<?php

namespace App\Services;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Cashier;
use Stripe\Customer as StripeCustomer;

class CashierService extends Cashier
{
    /**
     * Find a billable model by Stripe ID.
     * Automatically discovers and searches all models using the Billable trait.
     */
    public static function findBillable($stripeId)
    {
        $stripeId = $stripeId instanceof StripeCustomer ? $stripeId->id : $stripeId;

        if (! $stripeId) {
            return null;
        }

        // Get all billable models dynamically
        $billableModels = static::getBillableModels();

        // Search each billable model for the Stripe ID
        foreach ($billableModels as $model) {
            $builder = in_array(SoftDeletes::class, class_uses_recursive($model))
                ? $model::withTrashed()
                : new $model;

            $result = $builder->where('stripe_id', $stripeId)->first();

            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Get all models that use the Billable trait.
     *
     * @return array<string>
     */
    protected static function getBillableModels(): array
    {
        static $billableModels = null;

        if ($billableModels !== null) {
            return $billableModels;
        }

        $billableModels = [];
        $modelsPath = app_path('Models');

        if (! is_dir($modelsPath)) {
            return $billableModels;
        }

        $modelFiles = glob($modelsPath.'/*.php');

        foreach ($modelFiles as $file) {
            $className = 'App\\Models\\'.basename($file, '.php');

            if (! class_exists($className)) {
                continue;
            }

            // Check if the class uses the Billable trait
            if (in_array(\Laravel\Cashier\Billable::class, class_uses_recursive($className))) {
                $billableModels[] = $className;
            }
        }

        return $billableModels;
    }
}
