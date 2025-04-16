<?php

namespace App\Jobs;


use App\Services\OnlyFansScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ScrapeOnlyFansProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $username) {}

    /**
     * Execute the job.
     */
    public function handle(OnlyFansScraperService $scraper)
    {
        Log::info("Job Scraping {$this->username}: at " . now()->toDateTimeString());
        $scraper->scrape($this->username);
    }
}
