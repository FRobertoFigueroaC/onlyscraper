<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Profile;

class BatchScrapeOnlyFansProfiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $popular;

    /**
     * Create a new job instance.
     */
    public function __construct(bool $popular = false)
    {
        $this->popular = $popular;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Profile::query();

        if ($this->popular) {
            $query->where('likes', '>=', 100000);
        } else {
            $query->where('likes', '<', 100000);
        }

        $profiles = $query->get();

        foreach ($profiles as $profile) {
            ScrapeOnlyFansProfile::dispatch($profile->username);
        }

        logger()->info(
            "BatchScrapeOnlyFansProfiles: Dispatched scraping for {$profiles->count()} profiles (" . ($this->popular ? 'popular' : 'regular') . ")."
        );
    }
}
