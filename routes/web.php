<?php

use App\Http\Controllers\DataFastProxyController;
use App\Http\Middleware\EnsureNeedsOnboarding;
use App\Http\Middleware\EnsureOnboardingCompleted;
use App\Livewire\LinkInBio;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

Route::get('/r/{code}', App\Livewire\ReferralRedirect::class)->name('referral.redirect');

// Tier violation page (shown after unauthorized access attempt)
Route::get('/tier-violation', App\Livewire\TierViolation::class)->name('tier-violation');

// Custom signup pages (public routes for webinar signups)
Route::get('/signup/{slug}', App\Livewire\CustomSignup::class)->name('signup.show');

// Public Link in Bio pages
Route::get('/p/{username}', LinkInBio\Show::class)->name('public.link-in-bio');

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

        // Analytics & Reports
        Route::get('/analytics', App\Livewire\Admin\Analytics::class)->name('analytics');

        // Subscription & Payment Management
        Route::get('/pricing', App\Livewire\Admin\Pricing::class)->name('pricing');

        // Market Management
        Route::prefix('markets')->name('markets.')->group(function () {
            Route::get('/', App\Livewire\Admin\Markets\MarketIndex::class)->name('index');
            Route::get('/settings', App\Livewire\Admin\Markets\MarketSettings::class)->name('settings');
            Route::get('/{market}/edit', App\Livewire\Admin\Markets\MarketEdit::class)->name('edit');
            Route::get('/waitlist', App\Livewire\Admin\Markets\WaitlistManagement::class)->name('waitlist');
        });

        // Marketing Management
        Route::prefix('marketing')->name('marketing.')->group(function () {

            Route::prefix('landing-pages')->name('landing-pages.')->group(function () {
                Route::get('/', App\Livewire\Admin\LandingPages\LandingPageIndex::class)->name('index');
                Route::get('/create', App\Livewire\Admin\LandingPages\LandingPageCreate::class)->name('create');
                Route::get('/{landingPage}/edit', App\Livewire\Admin\LandingPages\LandingPageEdit::class)->name('edit');
            });

            Route::prefix('forms')->name('forms.')->group(function () {
                Route::get('/', App\Livewire\Admin\Forms\FormIndex::class)->name('index');
                Route::get('/create', App\Livewire\Admin\Forms\FormCreate::class)->name('create');
                Route::get('/{form}/edit', App\Livewire\Admin\Forms\FormEdit::class)->name('edit');
                Route::get('/{form}/submissions', App\Livewire\Admin\Forms\FormSubmissions::class)->name('submissions');
            });

            Route::prefix('email-sequences')->name('email-sequences.')->group(function () {
                Route::get('/', App\Livewire\Admin\EmailSequences\EmailSequenceIndex::class)->name('index');
                Route::get('/create', App\Livewire\Admin\EmailSequences\EmailSequenceCreate::class)->name('create');
                Route::get('/{emailSequence}/edit', App\Livewire\Admin\EmailSequences\EmailSequenceEdit::class)->name('edit');
            });

            Route::prefix('funnels')->name('funnels.')->group(function () {
                Route::get('/', App\Livewire\Admin\Funnels\FunnelIndex::class)->name('index');
                Route::get('/create', App\Livewire\Admin\Funnels\FunnelEdit::class)->name('create');
                Route::get('/{funnel}/edit', App\Livewire\Admin\Funnels\FunnelEdit::class)->name('edit')->where('funnel', '[0-9]+');
            });

        });

        // Referral Program Management
        Route::prefix('referrals')->name('referrals.')->group(function () {
            Route::get('/', App\Livewire\Admin\Referrals\ReferralIndex::class)->name('index');
            Route::get('/settings', App\Livewire\Admin\Referrals\ReferralSettings::class)->name('settings');
            Route::get('/review', App\Livewire\Admin\Referrals\ReferralReview::class)->name('review');
            Route::get('/percentages', App\Livewire\Admin\Referrals\ManagePercentages::class)->name('percentages');
            Route::get('/payouts', App\Livewire\Admin\Referrals\PayoutManagement::class)->name('payouts');
            Route::get('/{user}', App\Livewire\Admin\Referrals\ReferralShow::class)->name('show');
        });

        // Component Preview (dev tools)
        Route::get('/component-preview', App\Livewire\Admin\ComponentPreview::class)->name('component-preview');

        // System Settings
        Route::get('/settings', App\Livewire\Admin\Settings::class)->name('settings');

        // Custom Signup Pages
        Route::prefix('custom-signup-pages')->name('custom-signup-pages.')->group(function () {
            Route::get('/', App\Livewire\Admin\CustomSignupPages\CustomSignupPageIndex::class)->name('index');
            Route::get('/create', App\Livewire\Admin\CustomSignupPages\CustomSignupPageCreate::class)->name('create');
            Route::get('/{customSignupPage}/edit', App\Livewire\Admin\CustomSignupPages\CustomSignupPageEdit::class)->name('edit');
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
            Route::get('/create', App\Livewire\Campaigns\EditCampaign::class)->name('create');
            Route::get('/{campaign}', App\Livewire\Campaigns\ShowCampaign::class)->name('show');
            Route::get('/{campaign}/edit', App\Livewire\Campaigns\EditCampaign::class)->name('edit');
            Route::get('/{campaign}/applications', App\Livewire\Campaigns\CampaignApplications::class)->name('applications');
        });

        // Application routes
        Route::prefix('applications')->name('applications.')->group(function () {
            Route::get('/', App\Livewire\Applications\ApplicationsIndex::class)->name('index');
            Route::get('/{application}', App\Livewire\Applications\ViewApplication::class)->name('show');
        });

        // Collaboration review routes
        Route::prefix('collaborations')->name('collaborations.')->group(function () {
            Route::get('/{collaboration}/review', App\Livewire\Reviews\SubmitReview::class)->name('review');
            Route::get('/{collaboration}/reviews', App\Livewire\Reviews\ViewReviews::class)->name('reviews');
        });

        // Influencer campaign discovery
        Route::get('/discover', App\Livewire\Campaigns\InfluencerCampaigns::class)->name('discover');

        // Chat routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', App\Livewire\Chat::class)->name('index');
            Route::get('/{chatId}', App\Livewire\Chat::class)->name('show');
        });

        // Referral routes
        Route::get('/referrals', App\Livewire\Referrals\Index::class)->name('referral.index');

        // Profile routes
        Route::get('/profile', App\Livewire\Profile\EditProfile::class)->name('profile.edit');
        Route::get('/billing', function () {
            $user = auth()->user();

            if ($user->isBusinessAccount()) {
                return redirect()->route('business.settings', ['tab' => 'billing']);
            }

            if ($user->isInfluencerAccount()) {
                return redirect()->route('influencer.settings', ['tab' => 'billing']);
            }

            return redirect()->route('dashboard');
        })->name('billing');
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

        // Business routes (must come before wildcard routes)
        Route::prefix('business')->name('business.')->group(function () {
            Route::get('/settings/{tab?}/{subtab?}', App\Livewire\Business\BusinessSettings::class)->name('settings');
            Route::get('/{user}/campaigns', App\Livewire\Business\BusinessCampaigns::class)->name('campaigns');
        });

        // Influencer routes (must come before wildcard routes)
        Route::prefix('influencer')->name('influencer.')->group(function () {
            Route::get('/settings/{tab?}/{subtab?}', App\Livewire\Influencer\InfluencerSettings::class)->name('settings');
        });

        // Public profile route (supports both username and ID)
        Route::get('/influencer/{username}', App\Livewire\ViewInfluencerProfile::class)->name('influencer.profile');
        Route::get('/influencer/{username}/reviews', App\Livewire\Reviews\UserReviews::class)->name('influencer.reviews')->defaults('type', 'influencer');
        Route::get('/business/{username}', App\Livewire\ViewBusinessProfile::class)->name('business.profile');
        Route::get('/business/{username}/reviews', App\Livewire\Reviews\UserReviews::class)->name('business.reviews')->defaults('type', 'business');

        // Analytics route (business users only)
        Route::get('/analytics', App\Livewire\Analytics::class)->name('analytics');

        // Media Kit route (influencer users only)
        Route::get('/media-kit', App\Livewire\MediaKit::class)->name('media-kit');

        // Help route (formerly contact)
        Route::get('/help', App\Livewire\Contact::class)->name('help');

        // Link in Bio Routes
        Route::prefix('link-in-bio')->name('link-in-bio.')->group(function () {
            Route::get('/', LinkInBio\Index::class)->name('index');
        });
    });
});

Route::get('/js/dfscript.js', [DataFastProxyController::class, 'script']);

// Email Sequence Public Routes
Route::get('/unsubscribe/{subscriber}', function ($subscriberId) {
    $subscriber = \App\Models\EmailSequenceSubscriber::findOrFail($subscriberId);

    // Verify token
    $token = request('token');
    $validToken = hash_hmac('sha256', $subscriber->id, config('app.key'));

    if ($token !== $validToken) {
        abort(403);
    }

    app(\App\Services\EmailSequenceService::class)->unsubscribe($subscriber, 'user_clicked_link');

    return view('email-sequences.unsubscribed', compact('subscriber'));
})->name('email-sequence.unsubscribe');

// Tracking pixel for email opens
Route::get('/email/track/{send}', function ($sendId) {
    $send = \App\Models\EmailSequenceSend::findOrFail($sendId);
    $send->markAsOpened();

    // Return a 1x1 transparent GIF
    return response()->make(
        base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'),
        200,
        ['Content-Type' => 'image/gif']
    );
})->name('email-sequence.track');

// Click tracking for email links
Route::get('/email/click/{send}', function ($sendId, \Illuminate\Http\Request $request) {
    $send = \App\Models\EmailSequenceSend::findOrFail($sendId);
    $send->markAsClicked();

    // Get the original URL from query parameter
    $url = $request->query('url');

    if (! $url) {
        abort(404);
    }

    // Redirect to the original URL
    return redirect($url);
})->name('email-sequence.click');
Route::post('/api/events', [DataFastProxyController::class, 'events']);

require __DIR__.'/auth.php';
