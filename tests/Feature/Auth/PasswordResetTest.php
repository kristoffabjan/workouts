<?php

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('reset password link screen can be rendered for app panel', function () {
    $this->get('/app/password-reset/request')->assertOk();
});

test('reset password link screen can be rendered for admin panel', function () {
    $this->get('/admin/password-reset/request')->assertOk();
});

test('reset password link can be requested for app panel', function () {
    Notification::fake();

    $user = User::factory()->create();
    Team::factory()->hasAttached($user)->create();

    Filament::setCurrentPanel(Filament::getPanel('app'));

    Livewire::test(Filament::getCurrentPanel()->getRequestPasswordResetRouteAction())
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request')
        ->assertNotified();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password link can be requested for admin panel', function () {
    Notification::fake();

    $user = User::factory()->globalAdmin()->create();

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(Filament::getCurrentPanel()->getRequestPasswordResetRouteAction())
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request')
        ->assertNotified();

    Notification::assertSentTo($user, ResetPassword::class);
});
