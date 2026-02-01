<?php

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

test('login screen can be rendered', function () {
    $this->get('/app/login')->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();
    Team::factory()->hasAttached($user)->create();

    Filament::setCurrentPanel(Filament::getPanel('app'));

    Livewire::test(Filament::getCurrentPanel()->getLoginRouteAction())
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $this->assertAuthenticatedAs($user);
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    Filament::setCurrentPanel(Filament::getPanel('app'));

    Livewire::test(Filament::getCurrentPanel()->getLoginRouteAction())
        ->fillForm([
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();
    Team::factory()->hasAttached($user)->create();

    $this->actingAs($user)
        ->post('/app/logout')
        ->assertRedirect();

    $this->assertGuest();
});

test('admin login screen can be rendered', function () {
    $this->get('/admin/login')->assertOk();
});

test('admin users can authenticate using the login screen', function () {
    $user = User::factory()->globalAdmin()->create();

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(Filament::getCurrentPanel()->getLoginRouteAction())
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $this->assertAuthenticatedAs($user);
});

test('admin users can not authenticate with invalid password', function () {
    $user = User::factory()->globalAdmin()->create();

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(Filament::getCurrentPanel()->getLoginRouteAction())
        ->fillForm([
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

test('admin users can logout', function () {
    $user = User::factory()->globalAdmin()->create();

    $this->actingAs($user)
        ->post('/admin/logout')
        ->assertRedirect();

    $this->assertGuest();
});
