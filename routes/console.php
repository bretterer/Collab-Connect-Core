<?php

use App\Jobs\ProcessCampaignLifecycle;
use Illuminate\Support\Facades\Schedule;

Schedule::command('email-sequences:process')->everyMinute();

// Campaign lifecycle transitions - run hourly for timely transitions
Schedule::job(new ProcessCampaignLifecycle)
    ->hourly()
    ->withoutOverlapping()
    ->onFailure(fn () => \Illuminate\Support\Facades\Log::error('ProcessCampaignLifecycle job failed'));

// Review period expiration - run daily at 1:15am
Schedule::command('reviews:expire-periods')->dailyAt('01:15');

// Chat-related scheduled jobs
Schedule::job(new \App\Jobs\CheckUnreadChatMessages)->everyThirtyMinutes();
Schedule::job(new \App\Jobs\SendCampaignChatReminders)->dailyAt('08:00');

Schedule::job(new \App\Jobs\CalculateReferralPayouts)->monthlyOn(1, '06:00');
Schedule::job(new \App\Jobs\MonitorTemporaryReferralPayouts)->dailyAt('00:01');
Schedule::job(new \App\Jobs\PrepareReferralPayouts)->monthlyOn(1, '06:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 1))->monthlyOn(15, '10:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 2))->monthlyOn(18, '10:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 3))->monthlyOn(21, '10:00');

Schedule::job(new \App\Jobs\ReenableInfluencers)->dailyAt('02:00');
Schedule::job(new \App\Jobs\HandleProfilePromotionUpdates)->dailyAt('23:00');
Schedule::job(new \App\Jobs\HandleCampaignBoostUpdates)->dailyAt('23:30');

// Subscription credit reset - safety net in case webhook fails
Schedule::job(new \App\Jobs\ResetSubscriptionCredits)->dailyAt('00:05');

// Link in Bio Analytics cleanup - run daily at 3am
Schedule::job(new \App\Jobs\CleanupLinkInBioAnalytics)->dailyAt('03:00');
