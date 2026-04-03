<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:check-financial')->dailyAt('08:00');
Schedule::command('expire:subscriptions')->hourly();
Schedule::command('mark:past-due')->everyFifteenMinutes();
Schedule::command('process:usage-billing')->dailyAt('00:10');
Schedule::command('upsell:run')->dailyAt('10:00');
Schedule::command('churn:recover')->everyThirtyMinutes();
Schedule::command('trial:optimize')->dailyAt('11:00');
