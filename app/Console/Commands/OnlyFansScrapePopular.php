<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Profile;
use App\Jobs\ScrapeOnlyFansProfile;
use Illuminate\Support\Facades\Log;

class OnlyFansScrapePopular extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onlyfans:scrape-popular';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape profiles with 100k+ likes';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;

        Profile::where('likes', '>=', 100000)
            ->each(function ($profile) use (&$count) {
                ScrapeOnlyFansProfile::dispatch($profile->username);
                $count++;
            });

        Log::info("Queued {$count} popular profiles for scraping at " . now()->toDateTimeString());
        $this->info("Queued {$count} popular profiles for scraping.");
    }
}
