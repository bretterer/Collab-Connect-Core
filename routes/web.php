<?php

use App\Http\Middleware\EnsureOnboardingCompleted;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

// Broadcasting routes
Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::middleware(['auth', 'verified'])->group(function () {

    // Admin routes (protected by admin middleware)
    Route::prefix('admin')->name('admin.')->middleware(App\Http\Middleware\EnsureAdminAccess::class)->group(function () {
        Route::get('/dashboard', App\Livewire\Admin\Dashboard::class)->name('dashboard');

        // User management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', App\Livewire\Admin\Users\UserIndex::class)->name('index');
            Route::get('/{user}', App\Livewire\Admin\Users\UserShow::class)->name('show');
            Route::get('/{user}/edit', App\Livewire\Admin\Users\UserEdit::class)->name('edit');
        });

        Route::get('/beta-invites', App\Livewire\Admin\BetaInvites::class)->name('beta-invites');

        // Campaign management
        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/', App\Livewire\Admin\Campaigns\CampaignIndex::class)->name('index');
            Route::get('/{campaign}', App\Livewire\Admin\Campaigns\CampaignShow::class)->name('show');
            Route::get('/{campaign}/edit', App\Livewire\Admin\Campaigns\CampaignEdit::class)->name('edit');
        });

        // System settings
        Route::get('/settings', App\Livewire\Admin\Settings::class)->name('settings');

        // Analytics & Reports
        Route::get('/analytics', App\Livewire\Admin\Analytics::class)->name('analytics');

        // Subscription & Payment Management
        Route::prefix('payments')->name('payments.')->group(function () {
            // Route::get('/plans', App\Livewire\Admin\Payments\Plans::class)->name('plans');
        });

        Route::prefix('products')->name('products.')->group(function () {

        });

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

        // Analytics route (business users only)
        Route::get('/analytics', App\Livewire\Analytics::class)->name('analytics');

        // Media Kit route (influencer users only)
        Route::get('/media-kit', App\Livewire\MediaKit::class)->name('media-kit');

        // Help route (formerly contact)
        Route::get('/help', App\Livewire\Contact::class)->name('help');
    });
});

require __DIR__.'/auth.php';
