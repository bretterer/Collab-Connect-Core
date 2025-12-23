<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('schedule:publish-campaigns')->everyMinute();
Schedule::command('email-sequences:process')->everyMinute();

// Campaign lifecycle transitions - run daily at 1am
Schedule::command('campaigns:process-scheduled')->dailyAt('01:00');
Schedule::command('campaigns:start-due')->dailyAt('01:05');
Schedule::command('campaigns:complete-due')->dailyAt('01:10');

// Review period expiration - run daily at 1:15am
Schedule::command('reviews:expire-periods')->dailyAt('01:15');

// Chat-related scheduled jobs
Schedule::job(new \App\Jobs\CheckUnreadChatMessages)->everyThirtyMinutes();
Schedule::job(new \App\Jobs\SendCampaignChatReminders)->dailyAt('08:00');

Schedule::job(new \App\Jobs\CalculateReferralPayouts)->monthlyOn(1, '06:00');
Schedule::job(new \App\Jobs\MonitorTemporaryReferralPayouts)->dailyAt('00:01');
Schedule::job(new \App\Jobs\PrepareReferralPayouts)->monthlyOn(15, '06:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 1))->monthlyOn(15, '10:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 2))->monthlyOn(18, '10:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 3))->monthlyOn(21, '10:00');

Schedule::job(new \App\Jobs\ReenableInfluencers)->dailyAt('02:00');
Schedule::job(new \App\Jobs\HandleProfilePromotionUpdates)->dailyAt('23:00');

// Link in Bio Analytics cleanup - run daily at 3am
Schedule::job(new \App\Jobs\CleanupLinkInBioAnalytics)->dailyAt('03:00');
