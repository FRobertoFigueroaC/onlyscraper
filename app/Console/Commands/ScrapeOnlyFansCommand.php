<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OnlyFansScraperService;
use App\Models\Profile;

class ScrapeOnlyFansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onlyfans:scrape {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape an onlyfans profile and save/update it on BD.';

    public function __construct(private OnlyFansScraperService $scraper)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $username = $this->argument('username');

        $this->info("Scraping OnlyFans profile: {$username}");

        $data = $this->scraper->scrape($username);

        if (empty($data)) {
            $this->error("Error while trying to get profile.");
            return Command::FAILURE;
        }

        $profile = Profile::updateOrCreate(
            ['username' => $data['username']],
            [
                'name' => $data['name'],
                'bio' => $data['bio'],
                'likes' => $data['likes'],
            ]
        );

        $this->info("Perfil guardado: {$profile->username} ({$profile->likes} likes)");
        return Command::SUCCESS;
    }
}
