<?php

use App\Enums\TrainingStatus;
use App\Filament\Admin\Resources\Trainings\Pages\EditTraining;
use App\Filament\Admin\Resources\Trainings\Pages\ListTrainings;
use App\Filament\Admin\Resources\Trainings\Pages\ViewTraining;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->admin = User::factory()->globalAdmin()->create();
});

describe('Admin Training access control', function () {
    it('allows system admin to view trainings list', function () {
        $this->actingAs($this->admin);

        Livewire::test(ListTrainings::class)
            ->assertSuccessful();
    });

    it('denies non-admin users from viewing trainings list', function () {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user);

        Livewire::test(ListTrainings::class)
            ->assertForbidden();
    });
});

describe('Admin Training shows all trainings across teams', function () {
    it('shows trainings from all teams', function () {
        $team1 = Team::factory()->create(['name' => 'Team One']);
        $team2 = Team::factory()->create(['name' => 'Team Two']);
        $coach1 = User::factory()->coach($team1)->create();
        $coach2 = User::factory()->coach($team2)->create();

        $training1 = Training::factory()->forTeam($team1)->createdBy($coach1)->create(['title' => 'Team One Training']);
        $training2 = Training::factory()->forTeam($team2)->createdBy($coach2)->create(['title' => 'Team Two Training']);

        $this->actingAs($this->admin);

        Livewire::test(ListTrainings::class)
            ->assertCanSeeTableRecords([$training1, $training2]);
    });

    it('displays team name in table', function () {
        $team = Team::factory()->create(['name' => 'Test Team']);
        $coach = User::factory()->coach($team)->create();
        Training::factory()->forTeam($team)->createdBy($coach)->create(['title' => 'Test Training']);

        $this->actingAs($this->admin);

        Livewire::test(ListTrainings::class)
            ->assertSee('Test Team');
    });
});

describe('Admin Training view', function () {
    it('can view training details', function () {
        $team = Team::factory()->create();
        $coach = User::factory()->coach($team)->create();
        $training = Training::factory()->forTeam($team)->createdBy($coach)->create();

        $this->actingAs($this->admin);

        Livewire::test(ViewTraining::class, ['record' => $training->getRouteKey()])
            ->assertSuccessful();
    });
});

describe('Admin Training edit', function () {
    it('can edit any training', function () {
        $team = Team::factory()->create();
        $coach = User::factory()->coach($team)->create();
        $training = Training::factory()->forTeam($team)->createdBy($coach)->create(['title' => 'Original Title']);

        $this->actingAs($this->admin);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->fillForm([
                'title' => 'Updated Title',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($training->refresh()->title)->toBe('Updated Title');
    });

    it('can change training status', function () {
        $team = Team::factory()->create();
        $coach = User::factory()->coach($team)->create();
        $training = Training::factory()->forTeam($team)->createdBy($coach)->draft()->create();

        $this->actingAs($this->admin);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->fillForm([
                'status' => TrainingStatus::Completed,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($training->refresh()->status)->toBe(TrainingStatus::Completed);
    });
});

describe('Admin Training deletion', function () {
    it('can delete any training', function () {
        $team = Team::factory()->create();
        $coach = User::factory()->coach($team)->create();
        $training = Training::factory()->forTeam($team)->createdBy($coach)->create();

        $this->actingAs($this->admin);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->callAction('delete');

        expect(Training::withTrashed()->find($training->id)->deleted_at)->not->toBeNull();
    });
});

describe('Admin Training filtering', function () {
    it('can filter trainings by team', function () {
        $team1 = Team::factory()->create(['name' => 'Team One']);
        $team2 = Team::factory()->create(['name' => 'Team Two']);
        $coach1 = User::factory()->coach($team1)->create();
        $coach2 = User::factory()->coach($team2)->create();

        $training1 = Training::factory()->forTeam($team1)->createdBy($coach1)->create(['title' => 'Team One Training']);
        $training2 = Training::factory()->forTeam($team2)->createdBy($coach2)->create(['title' => 'Team Two Training']);

        $this->actingAs($this->admin);

        Livewire::test(ListTrainings::class)
            ->assertCanSeeTableRecords([$training1, $training2])
            ->filterTable('team_id', $team1->id)
            ->assertCanSeeTableRecords([$training1])
            ->assertCanNotSeeTableRecords([$training2]);
    });

    it('can filter trainings by status', function () {
        $team = Team::factory()->create();
        $coach = User::factory()->coach($team)->create();

        $draftTraining = Training::factory()->forTeam($team)->createdBy($coach)->draft()->create(['title' => 'Draft']);
        $scheduledTraining = Training::factory()->forTeam($team)->createdBy($coach)->scheduled()->create(['title' => 'Scheduled']);

        $this->actingAs($this->admin);

        Livewire::test(ListTrainings::class)
            ->assertCanSeeTableRecords([$draftTraining, $scheduledTraining])
            ->filterTable('status', TrainingStatus::Scheduled->value)
            ->assertCanSeeTableRecords([$scheduledTraining])
            ->assertCanNotSeeTableRecords([$draftTraining]);
    });

    it('can search trainings by title', function () {
        $team = Team::factory()->create();
        $coach = User::factory()->coach($team)->create();

        $morningTraining = Training::factory()->forTeam($team)->createdBy($coach)->create(['title' => 'Morning Workout']);
        $eveningTraining = Training::factory()->forTeam($team)->createdBy($coach)->create(['title' => 'Evening Session']);

        $this->actingAs($this->admin);

        Livewire::test(ListTrainings::class)
            ->searchTable('Morning')
            ->assertCanSeeTableRecords([$morningTraining])
            ->assertCanNotSeeTableRecords([$eveningTraining]);
    });
});
