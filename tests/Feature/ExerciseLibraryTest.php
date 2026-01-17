<?php

use App\Filament\App\Resources\Exercises\Pages\ListExercises;
use App\Models\Exercise;
use App\Models\Team;
use App\Models\User;
use App\Services\ExerciseLibraryService;
use Database\Seeders\GlobalExerciseSeeder;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->team = Team::factory()->create();
    $this->coach = User::factory()->coach($this->team)->create();
    $this->admin = User::factory()->globalAdmin()->create();
});

describe('GlobalExerciseSeeder', function () {
    it('creates global exercises', function () {
        $this->seed(GlobalExerciseSeeder::class);

        $globalExercises = Exercise::withoutGlobalScopes()->global()->count();

        expect($globalExercises)->toBeGreaterThan(50);
    });

    it('is idempotent - running twice creates same number of exercises', function () {
        $this->seed(GlobalExerciseSeeder::class);
        $countAfterFirst = Exercise::withoutGlobalScopes()->global()->count();

        $this->seed(GlobalExerciseSeeder::class);
        $countAfterSecond = Exercise::withoutGlobalScopes()->global()->count();

        expect($countAfterSecond)->toBe($countAfterFirst);
    });

    it('creates exercises with team_id null', function () {
        $this->seed(GlobalExerciseSeeder::class);

        $exercise = Exercise::withoutGlobalScopes()->global()->first();

        expect($exercise->team_id)->toBeNull();
    });

    it('creates exercises with tags', function () {
        $this->seed(GlobalExerciseSeeder::class);

        $exerciseWithTags = Exercise::withoutGlobalScopes()
            ->global()
            ->whereNotNull('tags')
            ->first();

        expect($exerciseWithTags->tags)->toBeArray()
            ->and($exerciseWithTags->tags)->not->toBeEmpty();
    });
});

describe('ExerciseLibraryService', function () {
    it('copies exercises to team', function () {
        $globalExercise = Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Global Bench Press']);

        $service = new ExerciseLibraryService;
        $copiedCount = $service->copyToTeam([$globalExercise->id], $this->team, $this->coach);

        expect($copiedCount)->toBe(1);

        $this->assertDatabaseHas('exercises', [
            'name' => 'Global Bench Press',
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);
    });

    it('skips exercises with same name already in team', function () {
        $globalExercise = Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Bench Press']);
        Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->create(['name' => 'Bench Press']);

        $service = new ExerciseLibraryService;
        $copiedCount = $service->copyToTeam([$globalExercise->id], $this->team, $this->coach);

        expect($copiedCount)->toBe(0);

        $teamExerciseCount = Exercise::withoutGlobalScopes()
            ->where('team_id', $this->team->id)
            ->where('name', 'Bench Press')
            ->count();

        expect($teamExerciseCount)->toBe(1);
    });

    it('copies exercise description and tags', function () {
        $globalExercise = Exercise::factory()->global()->createdBy($this->admin)->create([
            'name' => 'Squat',
            'description' => 'A compound exercise',
            'tags' => ['strength', 'lower-body', 'compound'],
            'video_urls' => ['https://youtube.com/watch?v=123'],
        ]);

        $service = new ExerciseLibraryService;
        $service->copyToTeam([$globalExercise->id], $this->team, $this->coach);

        $copiedExercise = Exercise::withoutGlobalScopes()
            ->where('team_id', $this->team->id)
            ->where('name', 'Squat')
            ->first();

        expect($copiedExercise->description)->toBe('A compound exercise')
            ->and($copiedExercise->tags)->toBe(['strength', 'lower-body', 'compound'])
            ->and($copiedExercise->video_urls)->toBe(['https://youtube.com/watch?v=123']);
    });

    it('copies multiple exercises at once', function () {
        $exercises = Exercise::factory()->global()->createdBy($this->admin)->count(3)->create();

        $service = new ExerciseLibraryService;
        $copiedCount = $service->copyToTeam($exercises->pluck('id')->toArray(), $this->team, $this->coach);

        expect($copiedCount)->toBe(3);

        $teamExerciseCount = Exercise::withoutGlobalScopes()
            ->where('team_id', $this->team->id)
            ->count();

        expect($teamExerciseCount)->toBe(3);
    });

    it('returns global exercises', function () {
        Exercise::factory()->global()->createdBy($this->admin)->count(5)->create();
        Exercise::factory()->forTeam($this->team)->createdBy($this->coach)->count(3)->create();

        $service = new ExerciseLibraryService;
        $globalExercises = $service->getGlobalExercises();

        expect($globalExercises)->toHaveCount(5);
    });

    it('searches global exercises by name', function () {
        Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Bench Press']);
        Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Back Squat']);
        Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Deadlift']);

        $service = new ExerciseLibraryService;
        $results = $service->searchGlobalExercises('Bench');

        expect($results)->toHaveCount(1)
            ->and($results->first()->name)->toBe('Bench Press');
    });

    it('searches global exercises by tag', function () {
        Exercise::factory()->global()->createdBy($this->admin)->create([
            'name' => 'Push-ups',
            'tags' => ['bodyweight', 'upper-body'],
        ]);
        Exercise::factory()->global()->createdBy($this->admin)->create([
            'name' => 'Running',
            'tags' => ['cardio', 'endurance'],
        ]);

        $service = new ExerciseLibraryService;
        $results = $service->searchGlobalExercises('bodyweight');

        expect($results)->toHaveCount(1)
            ->and($results->first()->name)->toBe('Push-ups');
    });
});

describe('Add from Library action', function () {
    it('shows add from library action for coach', function () {
        Filament::setCurrentPanel(Filament::getPanel('app'));
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListExercises::class)
            ->assertActionExists('addFromLibrary');
    });

    it('copies selected exercises when action is submitted', function () {
        $globalExercise = Exercise::factory()->global()->createdBy($this->admin)->create(['name' => 'Global Exercise']);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListExercises::class)
            ->callAction('addFromLibrary', [
                'exercises' => [$globalExercise->id],
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('exercises', [
            'name' => 'Global Exercise',
            'team_id' => $this->team->id,
        ]);
    });
});
