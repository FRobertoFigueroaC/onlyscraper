<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Profile;
use App\Jobs\ScrapeOnlyFansProfile;

class ScheduleOnlyFansScrapes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onlyfans:scrape-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'OnlyFans scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Profile::pluck('username')->each(function ($username) {
            ScrapeOnlyFansProfile::dispatch($username);
        });
    }
}
