<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('schedule:publish-campaigns')->everyMinute();

Schedule::job(new \App\Jobs\NotifyUsersOfStaleUnreadMessages)->hourly();
