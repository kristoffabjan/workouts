<?php

use App\Livewire\AcceptInvitation;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/invitation/accept/{token}', AcceptInvitation::class)
    ->name('invitation.accept');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
