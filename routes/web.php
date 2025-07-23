<?php

use App\Http\Middleware\EnsureOnboardingCompleted;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

Route::middleware(['auth'])->group(function () {
    // Onboarding routes (accessible before onboarding is completed)
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/account-type', App\Livewire\Onboarding\AccountTypeSelection::class)->name('account-type');
        Route::get('/business', App\Livewire\Onboarding\BusinessOnboarding::class)->name('business');
        Route::get('/influencer', App\Livewire\Onboarding\InfluencerOnboarding::class)->name('influencer');
    });

    // Main dashboard (protected by onboarding middleware)
    Route::middleware(EnsureOnboardingCompleted::class)->group(function () {
        Route::get('/dashboard', App\Livewire\Dashboard::class)->name('dashboard');

        Route::get('/search', App\Livewire\Search::class)->name('search');

        // Campaign routes
        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/', App\Livewire\Campaigns\Index::class)->name('index');
            Route::get('/create', App\Livewire\Campaigns\CreateCampaign::class)->name('create');
            Route::get('/{campaign}', App\Livewire\Campaigns\ShowCampaign::class)->name('show');
            Route::get('/{campaign}/edit', App\Livewire\Campaigns\EditCampaign::class)->name('edit');
        });

        // Influencer campaign discovery
        Route::get('/discover', App\Livewire\Campaigns\InfluencerCampaigns::class)->name('discover');
    });
});

require __DIR__.'/auth.php';
