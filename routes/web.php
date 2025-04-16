<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Profile;
use App\Services\OnlyFansScraperService;
use Illuminate\Http\Request;


Route::get('/', function () {
    return Inertia::render('Scraper/Search');
})->name('home');

Route::get('/search', function (Request $request) {
    $query = $request->get('q');

    if (!$query) {
        return response()->json(['results' => []]);
    }

    $results = Profile::search($query)->take(20)->get();

    return response()->json([
        'results' => $results,
    ]);
})->name('scrape.search');


// Route::get('/search', function (Request $request) {
//     $query = $request->get('q');

//     return Profile::search($query)->get();
// });

Route::get('/scrape/{username}', function ($username) {
    $scraper = new OnlyFansScraperService();
    return $scraper->scrape($username);
});



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
