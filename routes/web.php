<?php

use App\Http\Middleware\EnsureOnboardingCompleted;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

// Broadcasting routes
Broadcast::routes(['middleware' => ['web', 'auth']]);

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
            Route::get('/{campaign}/applications', App\Livewire\Campaigns\CampaignApplications::class)->name('applications');
        });

        // Application routes
        Route::prefix('applications')->name('applications.')->group(function () {
            Route::get('/', App\Livewire\Applications\ApplicationsIndex::class)->name('index');
            Route::get('/{application}', App\Livewire\Applications\ViewApplication::class)->name('show');
        });

        // Influencer campaign discovery
        Route::get('/discover', App\Livewire\Campaigns\InfluencerCampaigns::class)->name('discover');

        // Chat routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', App\Livewire\Chat::class)->name('index');
            Route::get('/{chatId}', App\Livewire\Chat::class)->name('show');
        });

        // Profile routes
        Route::get('/profile', App\Livewire\Profile\EditProfile::class)->name('profile');

        // Contact route
        Route::get('/contact', App\Livewire\Contact::class)->name('contact');
    });
});

require __DIR__.'/auth.php';
