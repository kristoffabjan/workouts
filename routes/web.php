<?php

use App\Livewire\AcceptInvitation;
use App\Livewire\RequestAccess;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/features', 'features')->name('features');
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => route('features'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => route('terms'), 'priority' => '0.3', 'changefreq' => 'yearly'],
        ['loc' => route('privacy'), 'priority' => '0.3', 'changefreq' => 'yearly'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ($urls as $url) {
        $xml .= '<url>';
        $xml .= '<loc>'.$url['loc'].'</loc>';
        $xml .= '<changefreq>'.$url['changefreq'].'</changefreq>';
        $xml .= '<priority>'.$url['priority'].'</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Route::get('/invitation/accept/{token}', AcceptInvitation::class)
    ->name('invitation.accept');

Route::get('/request-access', RequestAccess::class)
    ->name('request-access')
    ->middleware('guest');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
