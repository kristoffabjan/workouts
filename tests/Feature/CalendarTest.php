<?php

use App\Enums\TrainingStatus;
use App\Filament\App\Widgets\CalendarWidget;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('app'));

    $this->team = Team::factory()->create();
    $this->coach = User::factory()->coach($this->team)->create();
    $this->client = User::factory()->client($this->team)->create();
});

describe('Calendar page access', function () {
    it('allows coach to access calendar page', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        $response = $this->get("/app/{$this->team->slug}/calendar");
        $response->assertSuccessful();
    });

    it('allows client to access calendar page', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        $response = $this->get("/app/{$this->team->slug}/calendar");
        $response->assertSuccessful();
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get("/app/{$this->team->slug}/calendar");
        $response->assertRedirect('/app/login');
    });
});

describe('Calendar widget fetchEvents for coach', function () {
    beforeEach(function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);
    });

    it('returns all team trainings for coach', function () {
        $scheduledTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'Scheduled Training',
                'status' => TrainingStatus::Scheduled,
                'scheduled_at' => now()->addDays(1),
            ]);

        $unassignedTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'Unassigned Training',
                'status' => TrainingStatus::Draft,
                'scheduled_at' => now()->addDays(2),
            ]);

        $widget = new CalendarWidget;
        $events = $widget->fetchEvents([
            'start' => now()->startOfMonth()->toDateTimeString(),
            'end' => now()->endOfMonth()->addMonth()->toDateTimeString(),
            'timezone' => 'UTC',
        ]);

        expect($events)->toHaveCount(2);
        expect(collect($events)->pluck('title'))->toContain('Scheduled Training', 'Unassigned Training');
    });

    it('does not return trainings from other teams', function () {
        $otherTeam = Team::factory()->create();
        $otherCoach = User::factory()->coach($otherTeam)->create();

        $teamTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'My Team Training',
                'scheduled_at' => now()->addDays(1),
            ]);

        $otherTeamTraining = Training::factory()
            ->forTeam($otherTeam)
            ->createdBy($otherCoach)
            ->create([
                'title' => 'Other Team Training',
                'scheduled_at' => now()->addDays(1),
            ]);

        $widget = new CalendarWidget;
        $events = $widget->fetchEvents([
            'start' => now()->startOfMonth()->toDateTimeString(),
            'end' => now()->endOfMonth()->addMonth()->toDateTimeString(),
            'timezone' => 'UTC',
        ]);

        expect($events)->toHaveCount(1);
        expect($events[0]['title'])->toBe('My Team Training');
    });

    it('only returns trainings within date range', function () {
        $inRangeTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'In Range Training',
                'scheduled_at' => now()->addDays(5),
            ]);

        $outOfRangeTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'Out of Range Training',
                'scheduled_at' => now()->addMonths(3),
            ]);

        $widget = new CalendarWidget;
        $events = $widget->fetchEvents([
            'start' => now()->startOfMonth()->toDateTimeString(),
            'end' => now()->endOfMonth()->toDateTimeString(),
            'timezone' => 'UTC',
        ]);

        expect($events)->toHaveCount(1);
        expect($events[0]['title'])->toBe('In Range Training');
    });

    it('does not return trainings without scheduled_at', function () {
        $scheduledTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'Scheduled Training',
                'scheduled_at' => now()->addDays(1),
            ]);

        $unscheduledTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'Unscheduled Training',
                'scheduled_at' => null,
            ]);

        $widget = new CalendarWidget;
        $events = $widget->fetchEvents([
            'start' => now()->startOfMonth()->toDateTimeString(),
            'end' => now()->endOfMonth()->addMonth()->toDateTimeString(),
            'timezone' => 'UTC',
        ]);

        expect($events)->toHaveCount(1);
        expect($events[0]['title'])->toBe('Scheduled Training');
    });
});

describe('Calendar widget fetchEvents for client', function () {
    beforeEach(function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);
    });

    it('returns only assigned trainings for client', function () {
        $assignedTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->assignedTo($this->client)
            ->create([
                'title' => 'Assigned Training',
                'status' => TrainingStatus::Scheduled,
                'scheduled_at' => now()->addDays(1),
            ]);

        $unassignedTraining = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'Unassigned Training',
                'status' => TrainingStatus::Scheduled,
                'scheduled_at' => now()->addDays(2),
            ]);

        $widget = new CalendarWidget;
        $events = $widget->fetchEvents([
            'start' => now()->startOfMonth()->toDateTimeString(),
            'end' => now()->endOfMonth()->addMonth()->toDateTimeString(),
            'timezone' => 'UTC',
        ]);

        expect($events)->toHaveCount(1);
        expect($events[0]['title'])->toBe('Assigned Training');
    });

    it('shows correct color for completed training', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'Completed Training',
                'status' => TrainingStatus::Completed,
                'scheduled_at' => now()->subDays(1),
            ]);

        $training->assignedUsers()->attach($this->client->id, [
            'completed_at' => now(),
            'feedback' => 'Great workout!',
        ]);

        $widget = new CalendarWidget;
        $events = $widget->fetchEvents([
            'start' => now()->startOfMonth()->subMonth()->toDateTimeString(),
            'end' => now()->endOfMonth()->toDateTimeString(),
            'timezone' => 'UTC',
        ]);

        expect($events)->toHaveCount(1);
        expect($events[0]['backgroundColor'])->toBe('#5AB58A'); // brand success
        expect($events[0]['extendedProps']['isCompleted'])->toBeTrue();
    });
});

describe('Event data structure', function () {
    beforeEach(function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);
    });

    it('returns events with correct structure', function () {
        $training = Training::factory()
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->create([
                'title' => 'Test Training',
                'status' => TrainingStatus::Scheduled,
                'scheduled_at' => now()->addDays(1),
            ]);

        $widget = new CalendarWidget;
        $events = $widget->fetchEvents([
            'start' => now()->startOfMonth()->toDateTimeString(),
            'end' => now()->endOfMonth()->addMonth()->toDateTimeString(),
            'timezone' => 'UTC',
        ]);

        expect($events)->toHaveCount(1);
        $event = $events[0];

        expect($event)->toHaveKeys(['id', 'title', 'start', 'allDay', 'url', 'backgroundColor', 'borderColor', 'extendedProps']);
        expect($event['id'])->toBe($training->id);
        expect($event['title'])->toBe('Test Training');
        expect($event['allDay'])->toBeTrue();
        expect($event['url'])->toContain('/app/');
        expect($event['extendedProps']['status'])->toBe(TrainingStatus::Scheduled->value);
    });
});

describe('Performance with many trainings', function () {
    it('handles 100+ trainings efficiently', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Training::factory()
            ->count(100)
            ->forTeam($this->team)
            ->createdBy($this->coach)
            ->sequence(fn ($sequence) => [
                'scheduled_at' => now()->addDays($sequence->index % 30),
            ])
            ->create();

        $startTime = microtime(true);

        $widget = new CalendarWidget;
        $events = $widget->fetchEvents([
            'start' => now()->startOfMonth()->toDateTimeString(),
            'end' => now()->endOfMonth()->addMonth()->toDateTimeString(),
            'timezone' => 'UTC',
        ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        expect($events)->toHaveCount(100);
        expect($executionTime)->toBeLessThan(2); // Should complete in under 2 seconds
    });
});
