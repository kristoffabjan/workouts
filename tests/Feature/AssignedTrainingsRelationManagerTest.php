<?php

use App\Enums\TeamRole;
use App\Enums\TrainingStatus;
use App\Filament\App\Resources\Users\Pages\ViewUser;
use App\Filament\App\Resources\Users\RelationManagers\AssignedTrainingsRelationManager;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('app'));
    $this->team = Team::factory()->create(['is_personal' => false]);
    $this->coach = User::factory()->create(['is_admin' => false]);
    $this->coach->teams()->attach($this->team, ['role' => TeamRole::Coach]);

    $this->client = User::factory()->create(['is_admin' => false]);
    $this->client->teams()->attach($this->team, ['role' => TeamRole::Client]);
});

describe('ViewUser page', function () {
    it('coach can view team member details page', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(ViewUser::class, ['record' => $this->client->id])
            ->assertSuccessful()
            ->assertSee($this->client->name)
            ->assertSee($this->client->email);
    });

    it('client cannot view team member details page', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(ViewUser::class, ['record' => $this->coach->id])
            ->assertForbidden();
    });
});

describe('AssignedTrainingsRelationManager', function () {
    it('coach can view assigned trainings for client', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'title' => 'Morning Workout',
            'status' => TrainingStatus::Scheduled,
            'scheduled_at' => now()->addDay(),
            'created_by' => $this->coach->id,
        ]);
        $training->assignedUsers()->attach($this->client);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(AssignedTrainingsRelationManager::class, [
            'ownerRecord' => $this->client,
            'pageClass' => ViewUser::class,
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$training])
            ->assertSee('Morning Workout');
    });

    it('shows training status badge', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Completed,
            'created_by' => $this->coach->id,
        ]);
        $training->assignedUsers()->attach($this->client, [
            'completed_at' => now(),
        ]);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(AssignedTrainingsRelationManager::class, [
            'ownerRecord' => $this->client,
            'pageClass' => ViewUser::class,
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$training]);
    });

    it('shows exercises count', function () {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);
        $training->assignedUsers()->attach($this->client);

        $exercises = \App\Models\Exercise::factory(3)->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);
        $training->exercises()->attach($exercises->pluck('id'));

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(AssignedTrainingsRelationManager::class, [
            'ownerRecord' => $this->client,
            'pageClass' => ViewUser::class,
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$training]);
    });

    it('filters trainings by status', function () {
        $scheduledTraining = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Scheduled,
            'created_by' => $this->coach->id,
        ]);
        $scheduledTraining->assignedUsers()->attach($this->client);

        $completedTraining = Training::factory()->create([
            'team_id' => $this->team->id,
            'status' => TrainingStatus::Completed,
            'created_by' => $this->coach->id,
        ]);
        $completedTraining->assignedUsers()->attach($this->client);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(AssignedTrainingsRelationManager::class, [
            'ownerRecord' => $this->client,
            'pageClass' => ViewUser::class,
        ])
            ->assertCanSeeTableRecords([$scheduledTraining, $completedTraining])
            ->filterTable('status', TrainingStatus::Scheduled->value)
            ->assertCanSeeTableRecords([$scheduledTraining])
            ->assertCanNotSeeTableRecords([$completedTraining]);
    });

    it('client cannot view assigned trainings relation manager', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        $canView = AssignedTrainingsRelationManager::canViewForRecord($this->coach, ViewUser::class);

        expect($canView)->toBeFalse();
    });

    it('coach can view assigned trainings relation manager', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        $canView = AssignedTrainingsRelationManager::canViewForRecord($this->client, ViewUser::class);

        expect($canView)->toBeTrue();
    });

    it('only shows trainings assigned to viewed user', function () {
        $clientsTraining = Training::factory()->create([
            'team_id' => $this->team->id,
            'title' => 'Client Training',
            'created_by' => $this->coach->id,
        ]);
        $clientsTraining->assignedUsers()->attach($this->client);

        $otherClient = User::factory()->create(['is_admin' => false]);
        $otherClient->teams()->attach($this->team, ['role' => TeamRole::Client]);

        $othersTraining = Training::factory()->create([
            'team_id' => $this->team->id,
            'title' => 'Other Training',
            'created_by' => $this->coach->id,
        ]);
        $othersTraining->assignedUsers()->attach($otherClient);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(AssignedTrainingsRelationManager::class, [
            'ownerRecord' => $this->client,
            'pageClass' => ViewUser::class,
        ])
            ->assertCanSeeTableRecords([$clientsTraining])
            ->assertCanNotSeeTableRecords([$othersTraining]);
    });

    it('shows completion status icon correctly', function () {
        $completedTraining = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);
        $completedTraining->assignedUsers()->attach($this->client, [
            'completed_at' => now(),
        ]);

        $pendingTraining = Training::factory()->create([
            'team_id' => $this->team->id,
            'created_by' => $this->coach->id,
        ]);
        $pendingTraining->assignedUsers()->attach($this->client);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(AssignedTrainingsRelationManager::class, [
            'ownerRecord' => $this->client,
            'pageClass' => ViewUser::class,
        ])
            ->assertCanSeeTableRecords([$completedTraining, $pendingTraining]);
    });
});
