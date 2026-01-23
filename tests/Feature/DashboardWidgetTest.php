<?php

use App\Enums\TrainingStatus;
use App\Filament\Admin\Widgets\AdminStatsWidget;
use App\Filament\Admin\Widgets\RecentActivityWidget;
use App\Filament\App\Widgets\ClientStatsWidget;
use App\Filament\App\Widgets\CoachStatsWidget;
use App\Filament\App\Widgets\PendingFeedbackWidget;
use App\Filament\App\Widgets\RecentCompletionsWidget;
use App\Filament\App\Widgets\RecentFeedbackWidget;
use App\Filament\App\Widgets\UpcomingTrainingsWidget;
use App\Models\Exercise;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->team = Team::factory()->create(['is_personal' => false]);
    $this->coach = User::factory()->coach($this->team)->create();
    $this->client = User::factory()->client($this->team)->create();
    $this->admin = User::factory()->create(['is_admin' => true]);
});

describe('Client Dashboard Widgets', function () {
    describe('ClientStatsWidget', function () {
        it('can be viewed by clients', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(ClientStatsWidget::canView())->toBeTrue();
        });

        it('cannot be viewed by coaches', function () {
            $this->actingAs($this->coach);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(ClientStatsWidget::canView())->toBeFalse();
        });

        it('renders successfully', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(ClientStatsWidget::class)
                ->assertSuccessful();
        });

        it('shows correct completion counts', function () {
            $completedTraining = Training::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->assignedTo($this->client)
                ->create(['status' => TrainingStatus::Completed]);

            $this->client->assignedTrainings()
                ->updateExistingPivot($completedTraining->id, [
                    'completed_at' => now(),
                ]);

            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(ClientStatsWidget::class)
                ->assertSee('This Week')
                ->assertSee('This Month')
                ->assertSee('This Year');
        });
    });

    describe('UpcomingTrainingsWidget', function () {
        it('can be viewed by clients', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(UpcomingTrainingsWidget::canView())->toBeTrue();
        });

        it('cannot be viewed by coaches', function () {
            $this->actingAs($this->coach);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(UpcomingTrainingsWidget::canView())->toBeFalse();
        });

        it('renders successfully', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(UpcomingTrainingsWidget::class)
                ->assertSuccessful();
        });

        it('shows upcoming assigned trainings', function () {
            $upcomingTraining = Training::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->assignedTo($this->client)
                ->create([
                    'title' => 'Upcoming Training',
                    'status' => TrainingStatus::Scheduled,
                    'scheduled_at' => now()->addDays(2),
                ]);

            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(UpcomingTrainingsWidget::class)
                ->assertCanSeeTableRecords([$upcomingTraining]);
        });

        it('does not show trainings beyond configured days', function () {
            $distantTraining = Training::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->assignedTo($this->client)
                ->create([
                    'title' => 'Distant Training',
                    'status' => TrainingStatus::Scheduled,
                    'scheduled_at' => now()->addDays(10),
                ]);

            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(UpcomingTrainingsWidget::class)
                ->assertCanNotSeeTableRecords([$distantTraining]);
        });

        it('does not show unassigned trainings', function () {
            $unassignedTraining = Training::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->create([
                    'title' => 'Unassigned Training',
                    'status' => TrainingStatus::Scheduled,
                    'scheduled_at' => now()->addDays(1),
                ]);

            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(UpcomingTrainingsWidget::class)
                ->assertCanNotSeeTableRecords([$unassignedTraining]);
        });
    });

    describe('PendingFeedbackWidget', function () {
        it('can be viewed by clients', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(PendingFeedbackWidget::canView())->toBeTrue();
        });

        it('shows trainings without feedback', function () {
            $pastTraining = Training::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->assignedTo($this->client)
                ->create([
                    'title' => 'Past Training',
                    'status' => TrainingStatus::Scheduled,
                    'scheduled_at' => now()->subDays(1),
                ]);

            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(PendingFeedbackWidget::class)
                ->assertCanSeeTableRecords([$pastTraining]);
        });

        it('does not show completed trainings', function () {
            $completedTraining = Training::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->assignedTo($this->client)
                ->create([
                    'title' => 'Completed Training',
                    'status' => TrainingStatus::Completed,
                    'scheduled_at' => now()->subDays(1),
                ]);

            $this->client->assignedTrainings()
                ->updateExistingPivot($completedTraining->id, [
                    'completed_at' => now(),
                    'feedback' => 'Great session!',
                ]);

            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(PendingFeedbackWidget::class)
                ->assertCanNotSeeTableRecords([$completedTraining]);
        });
    });

    describe('RecentFeedbackWidget', function () {
        it('can be viewed by clients', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(RecentFeedbackWidget::canView())->toBeTrue();
        });

        it('renders successfully', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(RecentFeedbackWidget::class)
                ->assertSuccessful();
        });
    });
});

describe('Coach Dashboard Widgets', function () {
    describe('CoachStatsWidget', function () {
        it('can be viewed by coaches', function () {
            $this->actingAs($this->coach);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(CoachStatsWidget::canView())->toBeTrue();
        });

        it('cannot be viewed by clients', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(CoachStatsWidget::canView())->toBeFalse();
        });

        it('renders successfully', function () {
            $this->actingAs($this->coach);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(CoachStatsWidget::class)
                ->assertSuccessful();
        });

        it('shows correct stats', function () {
            Training::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->count(3)
                ->create();

            Exercise::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->count(5)
                ->create();

            $this->actingAs($this->coach);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(CoachStatsWidget::class)
                ->assertSee('Total Trainings')
                ->assertSee('Scheduled')
                ->assertSee('Completed')
                ->assertSee('Team Members')
                ->assertSee('Exercises');
        });
    });

    describe('RecentCompletionsWidget', function () {
        it('can be viewed by coaches', function () {
            $this->actingAs($this->coach);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(RecentCompletionsWidget::canView())->toBeTrue();
        });

        it('cannot be viewed by clients', function () {
            $this->actingAs($this->client);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            expect(RecentCompletionsWidget::canView())->toBeFalse();
        });

        it('renders successfully', function () {
            $this->actingAs($this->coach);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(RecentCompletionsWidget::class)
                ->assertSuccessful();
        });

        it('shows recent client completions', function () {
            $training = Training::factory()
                ->forTeam($this->team)
                ->createdBy($this->coach)
                ->assignedTo($this->client)
                ->create(['title' => 'Completed Training']);

            $this->client->assignedTrainings()
                ->updateExistingPivot($training->id, [
                    'completed_at' => now(),
                    'feedback' => 'Great workout!',
                ]);

            $this->actingAs($this->coach);
            Filament::setCurrentPanel(Filament::getPanel('app'));
            Filament::setTenant($this->team);

            Livewire::test(RecentCompletionsWidget::class)
                ->assertSee($this->client->name)
                ->assertSee('Completed Training')
                ->assertSee('Great workout!');
        });
    });
});

describe('Admin Dashboard Widgets', function () {
    describe('AdminStatsWidget', function () {
        it('renders successfully', function () {
            $this->actingAs($this->admin);
            Filament::setCurrentPanel(Filament::getPanel('admin'));

            Livewire::test(AdminStatsWidget::class)
                ->assertSuccessful();
        });

        it('shows global statistics', function () {
            Team::factory()->count(2)->create(['is_personal' => false]);
            Team::factory()->count(3)->create(['is_personal' => true]);
            User::factory()->count(5)->create(['is_admin' => false]);
            Training::factory()->count(10)->create();
            Exercise::factory()->count(8)->create();
            Exercise::factory()->count(5)->create(['team_id' => null]);

            $this->actingAs($this->admin);
            Filament::setCurrentPanel(Filament::getPanel('admin'));

            Livewire::test(AdminStatsWidget::class)
                ->assertSee('Total Teams')
                ->assertSee('Total Users')
                ->assertSee('Total Trainings')
                ->assertSee('Total Exercises');
        });
    });

    describe('RecentActivityWidget', function () {
        it('renders successfully', function () {
            $this->actingAs($this->admin);
            Filament::setCurrentPanel(Filament::getPanel('admin'));

            Livewire::test(RecentActivityWidget::class)
                ->assertSuccessful();
        });

        it('shows recent trainings from all teams', function () {
            $team1 = Team::factory()->create(['name' => 'Team One']);
            $team2 = Team::factory()->create(['name' => 'Team Two']);
            $coach1 = User::factory()->coach($team1)->create();
            $coach2 = User::factory()->coach($team2)->create();

            $training1 = Training::factory()
                ->forTeam($team1)
                ->createdBy($coach1)
                ->create(['title' => 'Training One']);
            $training2 = Training::factory()
                ->forTeam($team2)
                ->createdBy($coach2)
                ->create(['title' => 'Training Two']);

            $this->actingAs($this->admin);
            Filament::setCurrentPanel(Filament::getPanel('admin'));

            Livewire::test(RecentActivityWidget::class)
                ->assertCanSeeTableRecords([$training1, $training2]);
        });
    });
});

describe('Widget visibility based on role', function () {
    it('client only sees client widgets on dashboard', function () {
        $this->actingAs($this->client);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->team);

        expect(ClientStatsWidget::canView())->toBeTrue();
        expect(UpcomingTrainingsWidget::canView())->toBeTrue();
        expect(PendingFeedbackWidget::canView())->toBeTrue();
        expect(RecentFeedbackWidget::canView())->toBeTrue();
        expect(CoachStatsWidget::canView())->toBeFalse();
        expect(RecentCompletionsWidget::canView())->toBeFalse();
    });

    it('coach only sees coach widgets on dashboard', function () {
        $this->actingAs($this->coach);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->team);

        expect(ClientStatsWidget::canView())->toBeFalse();
        expect(UpcomingTrainingsWidget::canView())->toBeFalse();
        expect(PendingFeedbackWidget::canView())->toBeFalse();
        expect(RecentFeedbackWidget::canView())->toBeFalse();
        expect(CoachStatsWidget::canView())->toBeTrue();
        expect(RecentCompletionsWidget::canView())->toBeTrue();
    });
});
