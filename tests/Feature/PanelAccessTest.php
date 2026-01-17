<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {
    $this->adminPanel = Filament::getPanel('admin');
    $this->appPanel = Filament::getPanel('app');
});

describe('Admin Panel Access', function () {
    it('allows system admins to access admin panel', function () {
        $admin = User::factory()->create(['is_admin' => true]);

        expect($admin->canAccessPanel($this->adminPanel))->toBeTrue();
    });

    it('denies regular users access to admin panel', function () {
        $user = User::factory()->create(['is_admin' => false]);

        expect($user->canAccessPanel($this->adminPanel))->toBeFalse();
    });

    it('redirects unauthenticated users to admin login', function () {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    });

    it('allows authenticated system admin to access admin dashboard', function () {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    });

    it('denies regular user access to admin dashboard', function () {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    });
});

describe('App Panel Access', function () {
    it('allows regular users to access app panel', function () {
        $user = User::factory()->create(['is_admin' => false]);

        expect($user->canAccessPanel($this->appPanel))->toBeTrue();
    });

    it('denies system admins access to app panel', function () {
        $admin = User::factory()->create(['is_admin' => true]);

        expect($admin->canAccessPanel($this->appPanel))->toBeFalse();
    });

    it('redirects unauthenticated users to app login', function () {
        $this->get('/app')
            ->assertRedirect('/app/login');
    });

    it('allows authenticated regular user to access app panel with team', function () {
        $team = Team::factory()->create();
        $user = User::factory()->create(['is_admin' => false]);
        $user->teams()->attach($team, ['role' => TeamRole::Coach->value]);

        $this->actingAs($user)
            ->get("/app/{$team->slug}")
            ->assertOk();
    });

    it('denies system admin access to app panel', function () {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin->value]);

        $this->actingAs($admin)
            ->get("/app/{$team->slug}")
            ->assertForbidden();
    });
});

describe('Landing Page', function () {
    it('shows login links on landing page', function () {
        $this->get('/')
            ->assertOk()
            ->assertSee('User Login')
            ->assertSee('Admin Login');
    });
});
