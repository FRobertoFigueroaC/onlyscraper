<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Profile;


class OnlyFansScraperService
{

    private function parseLikesToInt(string $likesText): int
    {
        $likesText = trim($likesText);
        if (str_ends_with($likesText, 'K')) {
            return (int)(floatval(str_replace('K', '', $likesText)) * 1000);
        }
        if (str_ends_with($likesText, 'M')) {
            return (int)(floatval(str_replace('M', '', $likesText)) * 1000000);
        }
        return (int)preg_replace('/\D/', '', $likesText);
    }


    public function scrape(string $username): array
    {


        try {
            $response = Http::get(config('scraper.api_url') . '/scrape/' . $username);



            if ($response->failed()) {
                throw new \Exception("Could not get profile.");
            }

            $html = $response->json('html');
            $crawler = new Crawler($html);


            // Getting name (display name)
            $name =  $crawler->filter('.g-user-name.m-lg-size')->count()
                ? trim($crawler->filter('.g-user-name.m-lg-size')->first()->text())
                : null;

            // dd($name);

            // Getting username (without @)
            $userName = $crawler->filter('.g-user-username')->count()
                    ? $crawler->filter('.g-user-username')->first()->text()
                    : null;



            $usernameExtracted = trim(str_replace('@', '', $userName));
            // dd($name, $usernameExtracted, $userName,);


            // Getting bio
            $bio = $crawler->filter('.b-user-info__text.m-break-word')->count()
                ? $crawler->filter('.b-user-info__text.m-break-word')->first()->text()
                : null;
            // dd($name, $usernameExtracted, $bio);


            // Getting "likes"

            $likesText = $crawler
                ->filter('[aria-label="Likes"] .b-profile__sections__count.g-semibold')
                ->first()?->text() ?? '0';


            //  Casting likes (it could be "6.8K")
            $likes = $this->parseLikesToInt($likesText);

            //dd($name, $usernameExtracted, $bio, $likesText, $likes);

            $profile = Profile::updateOrCreate(
                ['username' => $usernameExtracted],
                [
                    'name' => $name,
                    'bio' => $bio,
                    'likes' => $likes,
                ]
            );

            return $profile->toArray();
        } catch (\Throwable $e) {
            Log::error(
                "Scraper error for {$username}: " . $e->getMessage()." at " . now()->toDateTimeString()
            );
            return [];
        }
    }
}
