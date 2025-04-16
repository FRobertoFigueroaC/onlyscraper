<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ScrapeOnlyFansProfile;

class GenerateOnlyFansUsernames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onlyfans:generate-usernames {prefix} {--dispatch} {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate likely OnlyFans usernames from a prefix and optionally dispatch scraping jobs';

    /**
     * Execute the console command.
     */
    public function handle():int
    {
        $prefix = $this->argument('prefix');
        $dispatch = $this->option('dispatch');
        $limit = (int) $this->option('limit');

        $this->info("Generating up to {$limit} usernames from prefix: {$prefix}");

        $usernames = $this->generateLikelyUsernames($prefix, $limit);

        foreach ($usernames as $username) {
            $this->line("- {$username}");

            if ($dispatch) {
                ScrapeOnlyFansProfile::dispatch($username);
                $this->info("  → Job dispatched for {$username}");
            }
        }

        return self::SUCCESS;
    }

    private function generateLikelyUsernames(string $prefix, int $limit = 100): array
    {
        $suffixes = [
            'a',
            'e',
            'y',
            'ie',
            'i',
            's',
            'z',
            '_',
            '.',
            '-',
            '90',
            '91',
            '92',
            '93',
            '94',
            '95',
            '96',
            '97',
            '98',
            '99',
            '00',
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '0',
            '_01',
            '_1',
            '-1',
            '_02',
            '_2',
            '-2',
            '_03',
            '_3',
            '-3',
            '_04',
            '_4',
            '-4',
            '_05',
            '_5',
            '-5',
            '_06',
            '_6',
            '-6',
            '_07',
            '_7',
            '-7',
            '_08',
            '_8',
            '-8',
            '_09',
            '_9',
            '-9',
        ];

        $variants = [];
        $variants[] = $prefix;

        // Simple mix
        foreach ($suffixes as $suffix) {
            $variants[] = $prefix . $suffix;
        }
        // Double mix
        foreach ($suffixes as $s1) {
            foreach ($suffixes as $s2) {
                $variants[] = $prefix . $s1 . $s2;
            }
        }

        $variants[] = $prefix . substr($prefix, -1);
        $variants[] = $prefix . 'xo';
        $variants[] = $prefix . 'ofc';
        $variants[] = $prefix . '_of';
        $variants[] = $prefix . 'of';
        $variants[] = $prefix . '_only';

        return array_slice(array_unique($variants), 0, $limit);
    }


}
