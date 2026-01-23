<?php

use App\Console\Commands\MarkMissedTrainings;
use App\Enums\TeamRole;
use App\Enums\TrainingStatus;
use App\Filament\App\Resources\Trainings\Pages\ViewTraining;
use App\Models\Exercise;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use App\Services\BulkScheduleService;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->team = Team::factory()->create();
    $this->coach = User::factory()->create();
    $this->client = User::factory()->create();

    $this->team->users()->attach($this->coach, ['role' => TeamRole::Coach->value]);
    $this->team->users()->attach($this->client, ['role' => TeamRole::Client->value]);
});

describe('Feedback Deadline Validation', function () {
    it('allows feedback submission within deadline', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->subDays(2),
            'created_by' => $this->coach->id,
        ]);

        $training->assignedUsers()->attach($this->client->id);

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->id])
            ->assertActionVisible('markAsComplete');
    });

    it('hides mark as complete action after feedback deadline', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->subDays(config('workouts.feedback_deadline_days', 3) + 1),
            'created_by' => $this->coach->id,
        ]);

        $training->assignedUsers()->attach($this->client->id);

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->id])
            ->assertActionHidden('markAsComplete');
    });

    it('hides edit feedback action after deadline', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Completed,
            'scheduled_at' => now()->subDays(config('workouts.feedback_deadline_days', 3) + 1),
            'created_by' => $this->coach->id,
        ]);

        $training->assignedUsers()->attach($this->client->id, [
            'completed_at' => now()->subDays(4),
            'feedback' => 'Initial feedback',
        ]);

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->id])
            ->assertActionHidden('editFeedback');
    });
});

describe('Mark Missed Trainings Command', function () {
    it('marks scheduled trainings as missed after deadline', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->subDays(config('workouts.missed_deadline_days', 3) + 1),
            'created_by' => $this->coach->id,
        ]);

        Artisan::call(MarkMissedTrainings::class);

        expect($training->fresh()->status)->toBe(TrainingStatus::Missed);
    });

    it('does not mark completed trainings as missed', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->subDays(config('workouts.missed_deadline_days', 3) + 1),
            'created_by' => $this->coach->id,
        ]);

        $training->assignedUsers()->attach($this->client->id, [
            'completed_at' => now()->subDays(2),
        ]);

        Artisan::call(MarkMissedTrainings::class);

        expect($training->fresh()->status)->toBe(TrainingStatus::Scheduled);
    });

    it('does not mark trainings within deadline as missed', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->subDays(1),
            'created_by' => $this->coach->id,
        ]);

        Artisan::call(MarkMissedTrainings::class);

        expect($training->fresh()->status)->toBe(TrainingStatus::Scheduled);
    });
});

describe('Scheduling Validation', function () {
    it('validates team membership when assigning users', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Draft,
            'created_by' => $this->coach->id,
        ]);

        $otherTeam = Team::factory()->create();
        $otherClient = User::factory()->create();
        $otherTeam->users()->attach($otherClient, ['role' => TeamRole::Client->value]);

        $service = app(BulkScheduleService::class);

        $service->scheduleTraining(
            $training,
            now()->addDay(),
            [$otherClient->id]
        );

        expect($training->fresh()->assignedUsers()->count())->toBe(0);
    });

    it('assigns only valid team members', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Draft,
            'created_by' => $this->coach->id,
        ]);

        $service = app(BulkScheduleService::class);

        $service->scheduleTraining(
            $training,
            now()->addDay(),
            [$this->client->id]
        );

        expect($training->fresh()->assignedUsers()->count())->toBe(1);
        expect($training->fresh()->assignedUsers->first()->id)->toBe($this->client->id);
    });
});

describe('Exercise Deletion Validation', function () {
    it('prevents deleting exercise attached to training', function () {
        $exercise = Exercise::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $training->exercises()->attach($exercise->id);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        expect($this->coach->can('delete', $exercise))->toBeFalse();
    });

    it('allows deleting exercise not attached to any training', function () {
        $exercise = Exercise::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        expect($this->coach->can('delete', $exercise))->toBeTrue();
    });
});

describe('User Deletion Validation', function () {
    it('prevents deleting user with created trainings', function () {
        Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        expect($admin->can('delete', $this->coach))->toBeFalse();
    });

    it('prevents deleting user with created exercises', function () {
        Exercise::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        expect($admin->can('delete', $this->coach))->toBeFalse();
    });

    it('allows deleting user without created content', function () {
        $newUser = User::factory()->create();
        $this->team->users()->attach($newUser, ['role' => TeamRole::Client->value]);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        expect($admin->can('delete', $newUser))->toBeTrue();
    });

    it('prevents self-deletion', function () {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        expect($admin->can('delete', $admin))->toBeFalse();
    });
});

describe('Past Training Edit Prevention', function () {
    it('prevents updating past scheduled training', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->subDay(),
            'created_by' => $this->coach->id,
        ]);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        expect($this->coach->can('update', $training))->toBeFalse();
    });

    it('allows updating future scheduled training', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->addDay(),
            'created_by' => $this->coach->id,
        ]);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        expect($this->coach->can('update', $training))->toBeTrue();
    });

    it('allows updating draft training without scheduled date', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Draft,
            'scheduled_at' => null,
            'created_by' => $this->coach->id,
        ]);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        expect($this->coach->can('update', $training))->toBeTrue();
    });

    it('admin can update past trainings', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->subDay(),
            'created_by' => $this->coach->id,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        expect($admin->can('update', $training))->toBeTrue();
    });
});

describe('Training Status Enum', function () {
    it('has missed status', function () {
        expect(TrainingStatus::Missed)->toBeInstanceOf(TrainingStatus::class);
        expect(TrainingStatus::Missed->value)->toBe('missed');
    });

    it('missed status has correct color', function () {
        expect(TrainingStatus::Missed->getColor())->toBe(\Filament\Support\Colors\Color::Red);
    });
});

describe('Config Values', function () {
    it('has feedback deadline days config', function () {
        expect(config('workouts.feedback_deadline_days'))->toBe(3);
    });

    it('has missed deadline days config', function () {
        expect(config('workouts.missed_deadline_days'))->toBe(3);
    });
});
