<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('schedule:publish-campaigns')->everyMinute();