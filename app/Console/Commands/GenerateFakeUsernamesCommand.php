<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Faker\Factory as Faker;
use App\Jobs\ScrapeOnlyFansProfile;

class GenerateFakeUsernamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onlyfans:generate-fake-usernames {--dispatch} {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create random usernames with Faker and optionally dispatch scraping jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $faker = Faker::create();
        $limit = (int) $this->option('limit');
        $shouldDispatch = $this->option('dispatch');

        $usernames = [];

        while (count($usernames) < $limit) {
            // Creates username
            $username = strtolower(preg_replace('/[^a-z0-9._-]/', '', $faker->userName));

            // Validates not duplicated usernames
            if (!in_array($username, $usernames)) {
                $usernames[] = $username;
                $this->info("username: {$username}");

                if ($shouldDispatch) {
                    ScrapeOnlyFansProfile::dispatch($username);
                    $this->line("→ dispatch for scraping.");
                }
            }
        }

        $this->info("✅ Total generated usernames: " . count($usernames));
        return Command::SUCCESS;
    }
}
