<?php

namespace App\Providers;

use App\Http\Controllers\CashierWebhookController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Http\Controllers\WebhookController;

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

        // Ensure mail components are properly loaded
        Blade::componentNamespace('Illuminate\\Mail\\Markdown\\Components', 'mail');

        $this->app->bind(WebhookController::class, function ($app) {
            return new CashierWebhookController;
        });

    }
}
