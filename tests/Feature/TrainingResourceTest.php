<?php

use App\Enums\TrainingStatus;
use App\Filament\App\Resources\Trainings\Pages\CreateTraining;
use App\Filament\App\Resources\Trainings\Pages\EditTraining;
use App\Filament\App\Resources\Trainings\Pages\ListTrainings;
use App\Filament\App\Resources\Trainings\Pages\ViewTraining;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use App\Notifications\TrainingCompletedNotification;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('app'));

    $this->team = Team::factory()->create();
    $this->coach = User::factory()->coach($this->team)->create();
    $this->client = User::factory()->client($this->team)->create();
});

describe('Training list access', function () {
    it('allows coach to view trainings list', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListTrainings::class)
            ->assertSuccessful();
    });

    it('allows client to view trainings list', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ListTrainings::class)
            ->assertSuccessful();
    });

    it('shows only team trainings for coach', function () {
        $otherTeam = Team::factory()->create();
        $otherCoach = User::factory()->coach($otherTeam)->create();
        $teamTraining = Training::factory()->forTeam($this->team)->createdBy($this->coach)->create(['title' => 'Team Training']);
        $otherTraining = Training::factory()->forTeam($otherTeam)->createdBy($otherCoach)->create(['title' => 'Other Team Training']);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListTrainings::class)
            ->assertCanSeeTableRecords([$teamTraining])
            ->assertCanNotSeeTableRecords([$otherTraining]);
    });

    it('shows only assigned trainings for client', function () {
        $assignedTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->create(['title' => 'Assigned Training']);
        $unassignedTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create(['title' => 'Unassigned Training']);

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ListTrainings::class)
            ->assertCanSeeTableRecords([$assignedTraining])
            ->assertCanNotSeeTableRecords([$unassignedTraining]);
    });
});

describe('Training view', function () {
    it('allows coach to view any team training', function () {
        $training = Training::factory()->forTeam($this->team)->createdBy($this->coach)->create();

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertSuccessful();
    });

    it('allows client to view assigned training', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertSuccessful();
    });

    it('prevents client from viewing unassigned training', function () {
        $training = Training::factory()->forTeam($this->team)->createdBy($this->coach)->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        // The query scope filters out unassigned trainings for clients,
        // so the record is not found (ModelNotFoundException) rather than forbidden
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()]);
    });
});

describe('Training creation', function () {
    it('allows coach to create training', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(CreateTraining::class)
            ->fillForm([
                'title' => 'New Training',
                'content' => 'Training content',
                'status' => TrainingStatus::Draft,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('trainings', [
            'title' => 'New Training',
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
            'status' => TrainingStatus::Draft->value,
        ]);
    });

    it('prevents client from creating training', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(CreateTraining::class)
            ->assertForbidden();
    });

    it('requires title field', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(CreateTraining::class)
            ->fillForm([
                'title' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    });

    it('can create training with scheduled_at datetime', function () {
        $scheduledAt = now()->addDays(3)->setHour(10)->setMinute(30);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(CreateTraining::class)
            ->fillForm([
                'title' => 'Scheduled Training',
                'status' => TrainingStatus::Scheduled,
                'scheduled_at' => $scheduledAt,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $training = Training::where('title', 'Scheduled Training')->first();
        expect($training->scheduled_at->format('Y-m-d H:i'))->toBe($scheduledAt->format('Y-m-d H:i'));
    });
});

describe('Training editing', function () {
    it('allows coach to edit training', function () {
        $training = Training::factory()->forTeam($this->team)->createdBy($this->coach)->create(['title' => 'Original Title']);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->fillForm([
                'title' => 'Updated Title',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($training->refresh()->title)->toBe('Updated Title');
    });

    it('prevents client from editing training', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->assertForbidden();
    });

    it('can update training status', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->draft()
            ->create();

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->fillForm([
                'status' => TrainingStatus::Scheduled,
                'scheduled_at' => now()->addDays(1),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($training->refresh()->status)->toBe(TrainingStatus::Scheduled);
    });
});

describe('Training deletion', function () {
    it('allows coach to delete training', function () {
        $training = Training::factory()->forTeam($this->team)->createdBy($this->coach)->create();

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->callAction('delete');

        expect(Training::withTrashed()->find($training->id)->deleted_at)->not->toBeNull();
    });
});

describe('Training filtering', function () {
    it('can filter trainings by status', function () {
        $draftTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->draft()
            ->create(['title' => 'Draft Training']);
        $scheduledTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->scheduled()
            ->create(['title' => 'Scheduled Training']);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListTrainings::class)
            ->assertCanSeeTableRecords([$draftTraining, $scheduledTraining])
            ->filterTable('status', TrainingStatus::Draft->value)
            ->assertCanSeeTableRecords([$draftTraining])
            ->assertCanNotSeeTableRecords([$scheduledTraining]);
    });

    it('can search trainings by title', function () {
        $morningTraining = Training::factory()->forTeam($this->team)->createdBy($this->coach)->create(['title' => 'Morning Workout']);
        $eveningTraining = Training::factory()->forTeam($this->team)->createdBy($this->coach)->create(['title' => 'Evening Session']);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListTrainings::class)
            ->searchTable('Morning')
            ->assertCanSeeTableRecords([$morningTraining])
            ->assertCanNotSeeTableRecords([$eveningTraining]);
    });

    it('can filter trainings by assigned user', function () {
        $otherClient = User::factory()->client($this->team)->create();

        $trainingForClient = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->create(['title' => 'Training for Client']);
        $trainingForOther = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($otherClient)
            ->create(['title' => 'Training for Other']);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ListTrainings::class)
            ->assertCanSeeTableRecords([$trainingForClient, $trainingForOther])
            ->filterTable('assigned_user', $this->client->id)
            ->assertCanSeeTableRecords([$trainingForClient])
            ->assertCanNotSeeTableRecords([$trainingForOther]);
    });
});

describe('Training mark as complete', function () {
    it('allows client to mark training as complete', function () {
        Notification::fake();

        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertActionVisible('markAsComplete')
            ->callAction('markAsComplete', [
                'feedback' => 'Great workout!',
            ]);

        $this->assertDatabaseHas('training_user', [
            'training_id' => $training->id,
            'user_id' => $this->client->id,
            'feedback' => 'Great workout!',
        ]);

        $pivot = $training->assignedUsers()->where('user_id', $this->client->id)->first()->pivot;
        expect($pivot->completed_at)->not->toBeNull();

        Notification::assertSentTo(
            $this->coach,
            TrainingCompletedNotification::class,
            function ($notification) use ($training) {
                return $notification->training->id === $training->id
                    && $notification->client->id === $this->client->id
                    && $notification->feedback === 'Great workout!';
            }
        );
    });

    it('allows client to mark training as complete without feedback', function () {
        Notification::fake();

        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->callAction('markAsComplete');

        $pivot = $training->assignedUsers()->where('user_id', $this->client->id)->first()->pivot;
        expect($pivot->completed_at)->not->toBeNull();
        expect($pivot->feedback)->toBeNull();

        Notification::assertSentTo($this->coach, TrainingCompletedNotification::class);
    });

    it('hides mark as complete action for coach', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertActionHidden('markAsComplete');
    });

    it('hides mark as complete action for future scheduled training', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->addDay())
            ->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertActionHidden('markAsComplete');
    });

    it('hides mark as complete action for already completed training', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $training->assignedUsers()->updateExistingPivot($this->client->id, [
            'completed_at' => now(),
        ]);

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertActionHidden('markAsComplete');
    });

    it('shows mark as complete action for training without scheduled_at', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->create(['scheduled_at' => null]);

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertActionVisible('markAsComplete');
    });

    it('sets training status to completed when marking as complete', function () {
        Notification::fake();

        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->callAction('markAsComplete');

        expect($training->refresh()->status)->toBe(TrainingStatus::Completed);
    });

    it('shows edit feedback action after completing training', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $training->assignedUsers()->updateExistingPivot($this->client->id, [
            'completed_at' => now(),
            'feedback' => 'Initial feedback',
        ]);

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertActionVisible('editFeedback')
            ->assertActionHidden('markAsComplete');
    });

    it('hides edit feedback action before completing training', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertActionHidden('editFeedback')
            ->assertActionVisible('markAsComplete');
    });

    it('allows client to edit feedback after completing training', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $training->assignedUsers()->updateExistingPivot($this->client->id, [
            'completed_at' => now(),
            'feedback' => 'Initial feedback',
        ]);

        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->callAction('editFeedback', [
                'feedback' => 'Updated feedback',
            ]);

        $this->assertDatabaseHas('training_user', [
            'training_id' => $training->id,
            'user_id' => $this->client->id,
            'feedback' => 'Updated feedback',
        ]);
    });

    it('hides edit feedback action for coach', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->scheduled(now()->subDay())
            ->create();

        $training->assignedUsers()->updateExistingPivot($this->client->id, [
            'completed_at' => now(),
        ]);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertActionHidden('editFeedback');
    });
});
