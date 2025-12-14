<?php

namespace App\Providers;

use App\Http\Controllers\CashierWebhookController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        EnsureFeaturesAreActive::whenInactive(
            function (Request $request, array $features) {
                return new Response(status: 404);
            }
        );

        // Ensure mail components are properly loaded
        Blade::componentNamespace('Illuminate\\Mail\\Markdown\\Components', 'mail');

        $this->app->bind(WebhookController::class, function ($app) {
            return new CashierWebhookController;
        });

        $this->app->bind('stripe', function ($app) {
            return new \Stripe\StripeClient(config('services.stripe.secret_key'));
        });

    }
}
