<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('marketing:pulse')->hourly();
Schedule::job(new \App\Jobs\SyncSocialMetricsJob)->dailyAt('02:00');
Schedule::job(new \App\Jobs\SyncGa4Job)->dailyAt('02:30');
