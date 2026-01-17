<?php

use App\Filament\Admin\Resources\Exercises\Pages\CreateExercise;
use App\Filament\Admin\Resources\Exercises\Pages\EditExercise;
use App\Filament\Admin\Resources\Exercises\Pages\ListExercises;
use App\Models\Exercise;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->admin = User::factory()->globalAdmin()->create();
});

describe('Admin Exercise access control', function () {
    it('allows system admin to view exercises list', function () {
        $this->actingAs($this->admin);

        Livewire::test(ListExercises::class)
            ->assertSuccessful();
    });

    it('denies non-admin users from viewing exercises list', function () {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user);

        Livewire::test(ListExercises::class)
            ->assertForbidden();
    });
});

describe('Admin Exercise shows only global exercises', function () {
    it('only shows global exercises (team_id = null)', function () {
        $globalExercise = Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Global Exercise']);
        $team = Team::factory()->create();
        $teamExercise = Exercise::factory()->forTeam($team)->create(['name' => 'Team Exercise']);

        $this->actingAs($this->admin);

        Livewire::test(ListExercises::class)
            ->assertCanSeeTableRecords([$globalExercise])
            ->assertCanNotSeeTableRecords([$teamExercise]);
    });
});

describe('Admin Exercise CRUD operations', function () {
    it('can render create page', function () {
        $this->actingAs($this->admin);

        Livewire::test(CreateExercise::class)
            ->assertSuccessful();
    });

    it('can create a global exercise', function () {
        $this->actingAs($this->admin);

        Livewire::test(CreateExercise::class)
            ->fillForm([
                'name' => 'New Global Exercise',
                'description' => 'A test exercise',
                'tags' => ['strength', 'compound'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('exercises', [
            'name' => 'New Global Exercise',
            'team_id' => null,
            'created_by' => $this->admin->id,
        ]);
    });

    it('can render edit page', function () {
        $exercise = Exercise::factory()->global()->createdBy($this->admin)->create();

        $this->actingAs($this->admin);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->assertSuccessful();
    });

    it('can update a global exercise', function () {
        $exercise = Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Original Name']);

        $this->actingAs($this->admin);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($exercise->refresh()->name)->toBe('Updated Name');
    });

    it('can delete a global exercise', function () {
        $exercise = Exercise::factory()->global()->createdBy($this->admin)->create();

        $this->actingAs($this->admin);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->callAction('delete');

        expect(Exercise::withTrashed()->find($exercise->id)->deleted_at)->not->toBeNull();
    });
});

describe('Admin Exercise validation', function () {
    it('requires name field', function () {
        $this->actingAs($this->admin);

        Livewire::test(CreateExercise::class)
            ->fillForm([
                'name' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('requires unique name for global exercises', function () {
        Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Existing Exercise']);

        $this->actingAs($this->admin);

        Livewire::test(CreateExercise::class)
            ->fillForm([
                'name' => 'Existing Exercise',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'unique']);
    });

    it('allows same name if existing exercise belongs to a team', function () {
        $team = Team::factory()->create();
        Exercise::factory()->forTeam($team)->create(['name' => 'Team Exercise']);

        $this->actingAs($this->admin);

        Livewire::test(CreateExercise::class)
            ->fillForm([
                'name' => 'Team Exercise',
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    });
});

describe('Admin Exercise filtering', function () {
    it('can filter exercises by tag', function () {
        $strengthExercise = Exercise::factory()->global()->createdBy($this->admin)->create([
            'name' => 'Strength Exercise',
            'tags' => ['strength', 'compound'],
        ]);
        $cardioExercise = Exercise::factory()->global()->createdBy($this->admin)->create([
            'name' => 'Cardio Exercise',
            'tags' => ['cardio', 'endurance'],
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListExercises::class)
            ->assertCanSeeTableRecords([$strengthExercise, $cardioExercise])
            ->filterTable('tags', ['strength'])
            ->assertCanSeeTableRecords([$strengthExercise])
            ->assertCanNotSeeTableRecords([$cardioExercise]);
    });

    it('can search exercises by name', function () {
        $benchPress = Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Bench Press']);
        $squat = Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Back Squat']);

        $this->actingAs($this->admin);

        Livewire::test(ListExercises::class)
            ->searchTable('Bench')
            ->assertCanSeeTableRecords([$benchPress])
            ->assertCanNotSeeTableRecords([$squat]);
    });
});
