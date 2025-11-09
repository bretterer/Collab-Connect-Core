<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('schedule:publish-campaigns')->everyMinute();

Schedule::job(new \App\Jobs\NotifyUsersOfStaleUnreadMessages)->hourly();

Schedule::job(new \App\Jobs\CalculateReferralPayouts)->monthlyOn(1, '06:00');
Schedule::job(new \App\Jobs\MonitorTemporaryReferralPayouts)->dailyAt('00:01');
