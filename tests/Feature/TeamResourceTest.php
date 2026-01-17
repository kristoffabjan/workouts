<?php

use App\Filament\Admin\Resources\Teams\Pages\CreateTeam;
use App\Filament\Admin\Resources\Teams\Pages\EditTeam;
use App\Filament\Admin\Resources\Teams\Pages\ListTeams;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

describe('TeamResource access control', function () {
    it('allows system admin to view teams list', function () {
        $admin = User::factory()->globalAdmin()->create();

        $this->actingAs($admin);

        Livewire::test(ListTeams::class)
            ->assertSuccessful();
    });

    it('denies non-admin users from viewing teams list', function () {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user);

        Livewire::test(ListTeams::class)
            ->assertForbidden();
    });

    it('denies team admin (non-system admin) from viewing teams list', function () {
        $teamAdmin = User::factory()->create(['is_admin' => false]);

        $this->actingAs($teamAdmin);

        Livewire::test(ListTeams::class)
            ->assertForbidden();
    });
});

describe('TeamResource CRUD operations', function () {
    it('can render create page', function () {
        $admin = User::factory()->globalAdmin()->create();

        $this->actingAs($admin);

        Livewire::test(CreateTeam::class)
            ->assertSuccessful();
    });

    it('can create a team', function () {
        $admin = User::factory()->globalAdmin()->create();

        $this->actingAs($admin);

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
        $admin = User::factory()->globalAdmin()->create();
        $teamToEdit = Team::factory()->create();

        $this->actingAs($admin);

        Livewire::test(EditTeam::class, ['record' => $teamToEdit->getRouteKey()])
            ->assertSuccessful();
    });

    it('can update a team', function () {
        $admin = User::factory()->globalAdmin()->create();
        $teamToEdit = Team::factory()->create(['name' => 'Original Name']);

        $this->actingAs($admin);

        Livewire::test(EditTeam::class, ['record' => $teamToEdit->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($teamToEdit->refresh()->name)->toBe('Updated Name');
    });

    it('can list all teams', function () {
        $admin = User::factory()->globalAdmin()->create();
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create(['name' => 'Other Team']);

        $this->actingAs($admin);

        Livewire::test(ListTeams::class)
            ->assertCanSeeTableRecords([$team1, $team2]);
    });
});

describe('TeamResource validation', function () {
    it('requires name field', function () {
        $admin = User::factory()->globalAdmin()->create();

        $this->actingAs($admin);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => '',
                'slug' => 'test-slug',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('requires slug field', function () {
        $admin = User::factory()->globalAdmin()->create();

        $this->actingAs($admin);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'Test Team',
                'slug' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'required']);
    });

    it('requires unique slug', function () {
        $admin = User::factory()->globalAdmin()->create();
        Team::factory()->create(['slug' => 'existing-slug']);

        $this->actingAs($admin);

        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name' => 'Test Team',
                'slug' => 'existing-slug',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    });
});
