<?php

use App\Enums\TeamRole;
use App\Filament\Resources\Teams\Pages\CreateTeam;
use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ListTeams;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

describe('TeamResource access control', function () {
    it('allows system admin to view teams list', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        Livewire::test(ListTeams::class)
            ->assertSuccessful();
    });

    it('denies non-admin users from viewing teams list', function () {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $user->teams()->attach($team, ['role' => TeamRole::Coach]);

        $this->actingAs($user);
        Filament::setTenant($team);

        Livewire::test(ListTeams::class)
            ->assertForbidden();
    });

    it('denies team admin (non-system admin) from viewing teams list', function () {
        $team = Team::factory()->create();
        $teamAdmin = User::factory()->create(['is_admin' => false]);
        $teamAdmin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($teamAdmin);
        Filament::setTenant($team);

        Livewire::test(ListTeams::class)
            ->assertForbidden();
    });
});

describe('TeamResource CRUD operations', function () {
    it('can render create page', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        Livewire::test(CreateTeam::class)
            ->assertSuccessful();
    });

    it('can create a team', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'New Test Team',
                'slug' => 'new-test-team',
                'is_personal' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('teams', [
            'name' => 'New Test Team',
            'slug' => 'new-test-team',
            'is_personal' => false,
        ]);
    });

    it('can render edit page', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        $teamToEdit = Team::factory()->create();

        Livewire::test(EditTeam::class, ['record' => $teamToEdit->getRouteKey()])
            ->assertSuccessful();
    });

    it('can update a team', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        $teamToEdit = Team::factory()->create(['name' => 'Original Name']);

        Livewire::test(EditTeam::class, ['record' => $teamToEdit->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($teamToEdit->refresh()->name)->toBe('Updated Name');
    });

    it('can list all teams regardless of tenant', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        $otherTeam = Team::factory()->create(['name' => 'Other Team']);

        Livewire::test(ListTeams::class)
            ->assertCanSeeTableRecords([$team, $otherTeam]);
    });
});

describe('TeamResource validation', function () {
    it('requires name field', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => '',
                'slug' => 'test-slug',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('requires slug field', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'Test Team',
                'slug' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'required']);
    });

    it('requires unique slug', function () {
        $team = Team::factory()->create();
        $admin = User::factory()->globalAdmin()->create();
        $admin->teams()->attach($team, ['role' => TeamRole::Admin]);

        $this->actingAs($admin);
        Filament::setTenant($team);

        Team::factory()->create(['slug' => 'existing-slug']);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'Test Team',
                'slug' => 'existing-slug',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    });
});
