<?php

use App\Livewire\AcceptInvitation;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/features', 'features')->name('features');
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');

Route::get('/invitation/accept/{token}', AcceptInvitation::class)
    ->name('invitation.accept');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
