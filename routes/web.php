<?php

use App\Http\Controllers\DataFastProxyController;
use App\Http\Middleware\EnsureNeedsOnboarding;
use App\Http\Middleware\EnsureOnboardingCompleted;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

// Broadcasting routes
Broadcast::routes(['middleware' => ['web', 'auth']]);

// Market waitlist route (accessible to authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/market-waitlist', App\Livewire\MarketWaitlist::class)->name('market-waitlist');
});

Route::middleware(['auth', 'verified', App\Http\Middleware\EnsureMarketApproved::class])->group(function () {

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
        Route::get('/feedback', App\Livewire\Admin\Feedback::class)->name('feedback');

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
        Route::get('/pricing', App\Livewire\Admin\Pricing::class)->name('pricing');

        // Market Management
        Route::prefix('markets')->name('markets.')->group(function () {
            Route::get('/', App\Livewire\Admin\Markets\MarketIndex::class)->name('index');
            Route::get('/{market}/edit', App\Livewire\Admin\Markets\MarketEdit::class)->name('edit');
            Route::get('/waitlist', App\Livewire\Admin\Markets\WaitlistManagement::class)->name('waitlist');
        });

    });

    Route::middleware(['auth', EnsureNeedsOnboarding::class])->group(function () {
        Route::get('/onboarding/business', App\Livewire\Onboarding\BusinessOnboarding::class)->name('onboarding.business');
        Route::get('/onboarding/influencer', App\Livewire\Onboarding\InfluencerOnboarding::class)->name('onboarding.influencer');
    });

    // Main dashboard (protected by onboarding middleware)
    Route::middleware(EnsureOnboardingCompleted::class)->group(function () {
        Route::get('/dashboard', App\Livewire\Dashboard::class)->name('dashboard');

        // Separate dashboard routes for different user types
        Route::get('/dashboard/business', App\Livewire\BusinessDashboard::class)->name('business.dashboard');
        Route::get('/dashboard/influencer', App\Livewire\InfluencerDashboard::class)->name('influencer.dashboard');

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
        Route::get('/profile', App\Livewire\Profile\EditProfile::class)->name('profile.edit');
        Route::get('/billing', App\Livewire\Profile\BillingDetails::class)->name('billing');
        Route::post('/switch-business/{business}', function (\App\Models\Business $business) {
            $user = auth()->user();

            // Verify user has access to this business
            if (! $user->businesses()->where('businesses.id', $business->id)->exists()) {
                abort(403);
            }

            // Set current business
            $user->setCurrentBusiness($business);

            return redirect()->back()->with('success', 'Switched to '.$business->name);
        })->name('switch-business');

        // Public profile route (supports both username and ID)
        Route::get('/influencer/{username}', App\Livewire\ViewInfluencerProfile::class)->name('influencer.profile');
        Route::get('/business/{username}', App\Livewire\ViewBusinessProfile::class)->name('business.profile');

        // Business routes
        Route::prefix('business')->name('business.')->group(function () {
            Route::get('/{user}/campaigns', App\Livewire\Business\BusinessCampaigns::class)->name('campaigns');
        });

        // Analytics route (business users only)
        Route::get('/analytics', App\Livewire\Analytics::class)->name('analytics');

        // Media Kit route (influencer users only)
        Route::get('/media-kit', App\Livewire\MediaKit::class)->name('media-kit');

        // Help route (formerly contact)
        Route::get('/help', App\Livewire\Contact::class)->name('help');
    });
});

Route::get('/js/dfscript.js', [DataFastProxyController::class, 'script']);
Route::post('/api/events', [DataFastProxyController::class, 'events']);

require __DIR__.'/auth.php';
