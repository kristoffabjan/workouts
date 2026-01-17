<?php

use App\Filament\App\Resources\Exercises\Pages\CreateExercise;
use App\Filament\App\Resources\Exercises\Pages\EditExercise;
use App\Filament\App\Resources\Exercises\Pages\ListExercises;
use App\Models\Exercise;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('app'));

    $this->team = Team::factory()->create();
    $this->coach = User::factory()->coach($this->team)->create();
    $this->client = User::factory()->client($this->team)->create();
});

describe('Exercise list access', function () {
    it('allows coach to view exercises list', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListExercises::class)
            ->assertSuccessful();
    });

    it('allows client to view exercises list', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ListExercises::class)
            ->assertSuccessful();
    });

    it('shows only team exercises', function () {
        $otherTeam = Team::factory()->create();
        $teamExercise = Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create(['name' => 'Team Exercise']);
        $otherExercise = Exercise::factory()->forTeam($otherTeam)->create(['name' => 'Other Team Exercise']);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListExercises::class)
            ->assertCanSeeTableRecords([$teamExercise])
            ->assertCanNotSeeTableRecords([$otherExercise]);
    });
});

describe('Exercise creation', function () {
    it('allows coach to create exercise', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(CreateExercise::class)
            ->fillForm([
                'name' => 'New Exercise',
                'description' => 'Test description',
                'tags' => ['strength', 'upper-body'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('exercises', [
            'name' => 'New Exercise',
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);
    });

    it('prevents client from creating exercise', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(CreateExercise::class)
            ->assertForbidden();
    });

    it('requires name field', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(CreateExercise::class)
            ->fillForm([
                'name' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });
});

describe('Exercise editing', function () {
    it('allows coach to edit exercise', function () {
        $exercise = Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create(['name' => 'Original Name']);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($exercise->refresh()->name)->toBe('Updated Name');
    });

    it('prevents client from editing exercise', function () {
        $exercise = Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->assertForbidden();
    });
});

describe('Exercise deletion', function () {
    it('allows coach to delete exercise', function () {
        $exercise = Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create();

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->callAction('delete');

        expect(Exercise::withTrashed()->find($exercise->id)->deleted_at)->not->toBeNull();
    });
});

describe('Exercise filtering', function () {
    it('can filter exercises by tag', function () {
        $strengthExercise = Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create([
            'name' => 'Strength Exercise',
            'tags' => ['strength', 'compound'],
        ]);
        $cardioExercise = Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create([
            'name' => 'Cardio Exercise',
            'tags' => ['cardio', 'endurance'],
        ]);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListExercises::class)
            ->assertCanSeeTableRecords([$strengthExercise, $cardioExercise])
            ->filterTable('tags', ['strength'])
            ->assertCanSeeTableRecords([$strengthExercise])
            ->assertCanNotSeeTableRecords([$cardioExercise]);
    });

    it('can search exercises by name', function () {
        $benchPress = Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create(['name' => 'Bench Press']);
        $squat = Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create(['name' => 'Back Squat']);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListExercises::class)
            ->searchTable('Bench')
            ->assertCanSeeTableRecords([$benchPress])
            ->assertCanNotSeeTableRecords([$squat]);
    });
});
