<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('schedule:publish-campaigns')->everyMinute();
Schedule::command('email-sequences:process')->everyMinute();

Schedule::job(new \App\Jobs\NotifyUsersOfStaleUnreadMessages)->hourly();

Schedule::job(new \App\Jobs\CalculateReferralPayouts)->monthlyOn(1, '06:00');
Schedule::job(new \App\Jobs\MonitorTemporaryReferralPayouts)->dailyAt('00:01');
Schedule::job(new \App\Jobs\PrepareReferralPayouts)->monthlyOn(15, '06:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 1))->monthlyOn(15, '10:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 2))->monthlyOn(18, '10:00');
Schedule::job(new \App\Jobs\ProcessReferralPayouts(attemptNumber: 3))->monthlyOn(21, '10:00');
