<?php

use App\Enums\TeamRole;
use App\Enums\TrainingStatus;
use App\Models\Exercise;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use App\Services\BulkScheduleService;
use Carbon\Carbon;

beforeEach(function () {
    $this->team = Team::factory()->create();
    $this->coach = User::factory()->create();
    $this->coach->teams()->attach($this->team, ['role' => TeamRole::Coach]);

    $this->client1 = User::factory()->create();
    $this->client1->teams()->attach($this->team, ['role' => TeamRole::Client]);

    $this->client2 = User::factory()->create();
    $this->client2->teams()->attach($this->team, ['role' => TeamRole::Client]);

    $this->actingAs($this->coach);

    $this->service = app(BulkScheduleService::class);
});

describe('scheduleTraining', function () {
    it('schedules a training with date and status', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
            'status' => TrainingStatus::Draft,
            'scheduled_at' => null,
        ]);

        $scheduledAt = now()->addDay();

        $result = $this->service->scheduleTraining($training, $scheduledAt);

        expect($result->status)->toBe(TrainingStatus::Scheduled)
            ->and($result->scheduled_at->toDateString())->toBe($scheduledAt->toDateString());
    });

    it('assigns users when scheduling', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
            'status' => TrainingStatus::Draft,
        ]);

        $this->service->scheduleTraining(
            $training,
            now()->addDay(),
            [$this->client1->id, $this->client2->id]
        );

        expect($training->assignedUsers()->count())->toBe(2)
            ->and($training->assignedUsers()->pluck('user_id')->toArray())
            ->toContain($this->client1->id, $this->client2->id);
    });

    it('does not duplicate user assignments', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $training->assignedUsers()->attach($this->client1->id);

        $this->service->scheduleTraining(
            $training,
            now()->addDay(),
            [$this->client1->id, $this->client2->id]
        );

        expect($training->assignedUsers()->count())->toBe(2);
    });
});

describe('duplicateTraining', function () {
    it('creates a copy of the training', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'title' => 'Original Training',
            'content' => 'Training content',
            'created_by' => $this->coach->id,
            'status' => TrainingStatus::Draft,
        ]);

        $newDate = now()->addWeek();
        $duplicate = $this->service->duplicateTraining($training, $newDate);

        expect($duplicate->id)->not->toBe($training->id)
            ->and($duplicate->title)->toBe('Original Training')
            ->and($duplicate->content)->toBe('Training content')
            ->and($duplicate->team_id)->toBe($this->team->id)
            ->and($duplicate->status)->toBe(TrainingStatus::Scheduled)
            ->and($duplicate->scheduled_at->toDateString())->toBe($newDate->toDateString());
    });

    it('copies exercises with pivot data', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $exercise1 = Exercise::factory()->create(['team_id' => $this->team->id]);
        $exercise2 = Exercise::factory()->create(['team_id' => $this->team->id]);

        $training->exercises()->attach($exercise1->id, ['notes' => 'Note 1', 'sort_order' => 1]);
        $training->exercises()->attach($exercise2->id, ['notes' => 'Note 2', 'sort_order' => 2]);

        $duplicate = $this->service->duplicateTraining($training, now()->addWeek());

        expect($duplicate->exercises()->count())->toBe(2);

        $duplicatedExercises = $duplicate->exercises()->orderByPivot('sort_order')->get();

        expect($duplicatedExercises[0]->id)->toBe($exercise1->id)
            ->and($duplicatedExercises[0]->pivot->notes)->toBe('Note 1')
            ->and($duplicatedExercises[0]->pivot->sort_order)->toBe(1)
            ->and($duplicatedExercises[1]->id)->toBe($exercise2->id)
            ->and($duplicatedExercises[1]->pivot->notes)->toBe('Note 2')
            ->and($duplicatedExercises[1]->pivot->sort_order)->toBe(2);
    });

    it('can skip copying exercises', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $exercise = Exercise::factory()->create(['team_id' => $this->team->id]);
        $training->exercises()->attach($exercise->id);

        $duplicate = $this->service->duplicateTraining($training, now()->addWeek(), copyExercises: false);

        expect($duplicate->exercises()->count())->toBe(0);
    });
});

describe('duplicateToMultipleDates', function () {
    it('creates multiple copies for multiple dates', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $dates = [
            now()->addWeek()->format('Y-m-d'),
            now()->addWeeks(2)->format('Y-m-d'),
            now()->addWeeks(3)->format('Y-m-d'),
        ];

        $count = $this->service->duplicateToMultipleDates($training, $dates);

        expect($count)->toBe(3)
            ->and(Training::where('team_id', $this->team->id)->count())->toBe(4);
    });

    it('assigns users to all duplicates', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $dates = [
            now()->addWeek()->format('Y-m-d'),
            now()->addWeeks(2)->format('Y-m-d'),
        ];

        $this->service->duplicateToMultipleDates(
            $training,
            $dates,
            [$this->client1->id]
        );

        $duplicates = Training::where('team_id', $this->team->id)
            ->where('id', '!=', $training->id)
            ->get();

        foreach ($duplicates as $duplicate) {
            expect($duplicate->assignedUsers()->count())->toBe(1)
                ->and($duplicate->assignedUsers()->first()->id)->toBe($this->client1->id);
        }
    });

    it('copies original user assignments when no users specified', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $training->assignedUsers()->attach([$this->client1->id, $this->client2->id]);

        $dates = [now()->addWeek()->format('Y-m-d')];

        $this->service->duplicateToMultipleDates($training, $dates);

        $duplicate = Training::where('team_id', $this->team->id)
            ->where('id', '!=', $training->id)
            ->first();

        expect($duplicate->assignedUsers()->count())->toBe(2);
    });

    it('uses database transaction for bulk creation', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $dates = array_map(
            fn ($i) => now()->addWeeks($i)->format('Y-m-d'),
            range(1, 20)
        );

        $count = $this->service->duplicateToMultipleDates($training, $dates);

        expect($count)->toBe(20)
            ->and(Training::where('team_id', $this->team->id)->count())->toBe(21);
    });
});

describe('generateWeeklyDates', function () {
    it('generates dates for specified number of weeks', function () {
        $startDate = Carbon::parse('2025-01-06'); // Monday

        $dates = $this->service->generateWeeklyDates($startDate, 4);

        expect($dates)->toHaveCount(4);

        $expectedDates = [
            '2025-01-06',
            '2025-01-13',
            '2025-01-20',
            '2025-01-27',
        ];

        foreach ($expectedDates as $index => $expected) {
            expect(Carbon::parse($dates[$index])->toDateString())->toBe($expected);
        }
    });

    it('generates dates for multiple days per week', function () {
        $startDate = Carbon::parse('2025-01-06'); // Monday

        $dates = $this->service->generateWeeklyDates($startDate, 2, [1, 3, 5]); // Mon, Wed, Fri

        expect($dates)->toHaveCount(6);
    });

    it('uses start date day when no days specified', function () {
        $startDate = Carbon::parse('2025-01-08'); // Wednesday

        $dates = $this->service->generateWeeklyDates($startDate, 3);

        foreach ($dates as $date) {
            expect(Carbon::parse($date)->dayOfWeek)->toBe(3); // Wednesday
        }
    });
});
