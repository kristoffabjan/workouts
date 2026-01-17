<?php

use App\Enums\TeamRole;
use App\Models\Exercise;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {
    $this->teamA = Team::factory()->create(['name' => 'Team A', 'slug' => 'team-a']);
    $this->teamB = Team::factory()->create(['name' => 'Team B', 'slug' => 'team-b']);

    $this->user = User::factory()->create();
    $this->user->teams()->attach($this->teamA, ['role' => TeamRole::Coach]);
    $this->user->teams()->attach($this->teamB, ['role' => TeamRole::Coach]);

    $this->actingAs($this->user);
});

describe('Exercise tenant scoping', function () {
    it('scopes exercises to the current tenant', function () {
        Filament::setTenant($this->teamA);

        $teamAExercise = Exercise::factory()->forTeam($this->teamA)->create(['name' => 'Team A Exercise']);
        $teamBExercise = Exercise::factory()->forTeam($this->teamB)->create(['name' => 'Team B Exercise']);

        $exercises = Exercise::all();

        expect($exercises)->toHaveCount(1)
            ->and($exercises->first()->name)->toBe('Team A Exercise');
    });

    it('shows different exercises when switching tenants', function () {
        $teamAExercise = Exercise::factory()->forTeam($this->teamA)->create(['name' => 'Team A Exercise']);
        $teamBExercise = Exercise::factory()->forTeam($this->teamB)->create(['name' => 'Team B Exercise']);

        Filament::setTenant($this->teamA);
        expect(Exercise::all())->toHaveCount(1)
            ->and(Exercise::first()->name)->toBe('Team A Exercise');

        Filament::setTenant($this->teamB);
        expect(Exercise::all())->toHaveCount(1)
            ->and(Exercise::first()->name)->toBe('Team B Exercise');
    });

    it('automatically assigns team_id when creating exercise in tenant context', function () {
        Filament::setTenant($this->teamA);

        $exercise = Exercise::create([
            'name' => 'New Exercise',
            'created_by' => $this->user->id,
        ]);

        expect($exercise->team_id)->toBe($this->teamA->id);
    });
});

describe('Training tenant scoping', function () {
    it('scopes trainings to the current tenant', function () {
        Filament::setTenant($this->teamA);

        $teamATraining = Training::factory()->forTeam($this->teamA)->create(['title' => 'Team A Training']);
        $teamBTraining = Training::factory()->forTeam($this->teamB)->create(['title' => 'Team B Training']);

        $trainings = Training::all();

        expect($trainings)->toHaveCount(1)
            ->and($trainings->first()->title)->toBe('Team A Training');
    });

    it('shows different trainings when switching tenants', function () {
        $teamATraining = Training::factory()->forTeam($this->teamA)->create(['title' => 'Team A Training']);
        $teamBTraining = Training::factory()->forTeam($this->teamB)->create(['title' => 'Team B Training']);

        Filament::setTenant($this->teamA);
        expect(Training::all())->toHaveCount(1)
            ->and(Training::first()->title)->toBe('Team A Training');

        Filament::setTenant($this->teamB);
        expect(Training::all())->toHaveCount(1)
            ->and(Training::first()->title)->toBe('Team B Training');
    });

    it('automatically assigns team_id when creating training in tenant context', function () {
        Filament::setTenant($this->teamA);

        $training = Training::create([
            'title' => 'New Training',
            'created_by' => $this->user->id,
        ]);

        expect($training->team_id)->toBe($this->teamA->id);
    });
});

describe('User tenant access', function () {
    it('returns teams the user belongs to', function () {
        $panel = Filament::getPanel('admin');
        $tenants = $this->user->getTenants($panel);

        expect($tenants)->toHaveCount(3)
            ->and($tenants->pluck('id')->toArray())->toContain($this->teamA->id, $this->teamB->id)
            ->and($this->user->hasPersonalTeam())->toBeTrue();
    });

    it('can access tenant the user belongs to', function () {
        expect($this->user->canAccessTenant($this->teamA))->toBeTrue()
            ->and($this->user->canAccessTenant($this->teamB))->toBeTrue();
    });

    it('cannot access tenant the user does not belong to', function () {
        $otherTeam = Team::factory()->create();

        expect($this->user->canAccessTenant($otherTeam))->toBeFalse();
    });
});

describe('Queries without tenant context', function () {
    it('does not filter exercises when no tenant is set', function () {
        $teamAExercise = Exercise::factory()->forTeam($this->teamA)->create();
        $teamBExercise = Exercise::factory()->forTeam($this->teamB)->create();

        $exercises = Exercise::all();

        expect($exercises)->toHaveCount(2);
    });

    it('does not filter trainings when no tenant is set', function () {
        $teamATraining = Training::factory()->forTeam($this->teamA)->create();
        $teamBTraining = Training::factory()->forTeam($this->teamB)->create();

        $trainings = Training::all();

        expect($trainings)->toHaveCount(2);
    });
});
