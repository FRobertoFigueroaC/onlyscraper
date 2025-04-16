<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Jobs\BatchScrapeOnlyFansProfiles;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



// Schedule::job(new BatchScrapeOnlyFansProfiles(true))
//     ->dailyAt('03:00');

// Schedule::job(new BatchScrapeOnlyFansProfiles(false))
//     ->cron('0 3 */3 * *'); // 72h


Schedule::job(new BatchScrapeOnlyFansProfiles(true))
    ->everyFiveMinutes();

Schedule::job(new BatchScrapeOnlyFansProfiles(false))
    ->everyTenMinutes();
