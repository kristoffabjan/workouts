<?php

namespace App\Filament\App\Widgets;

use App\Enums\TrainingStatus;
use App\Models\Exercise;
use App\Models\Training;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CoachStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        return $user && $team && $user->isCoach($team);
    }

    protected function getStats(): array
    {
        $team = Filament::getTenant();

        $totalTrainings = Training::query()
            ->where('team_id', $team->id)
            ->count();

        $scheduledTrainings = Training::query()
            ->where('team_id', $team->id)
            ->where('status', TrainingStatus::Scheduled)
            ->count();

        $completedTrainings = Training::query()
            ->where('team_id', $team->id)
            ->where('status', TrainingStatus::Completed)
            ->count();

        $teamMembers = $team->users()->count();

        $exercises = Exercise::query()
            ->where('team_id', $team->id)
            ->count();

        return [
            Stat::make('Total Trainings', $totalTrainings)
                ->description('In this team')
                ->descriptionIcon(Heroicon::ClipboardDocumentList)
                ->color('primary'),
            Stat::make('Scheduled', $scheduledTrainings)
                ->description('Upcoming trainings')
                ->descriptionIcon(Heroicon::Calendar)
                ->color('info'),
            Stat::make('Completed', $completedTrainings)
                ->description('Finished trainings')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('success'),
            Stat::make('Team Members', $teamMembers)
                ->description('Coaches & clients')
                ->descriptionIcon(Heroicon::UserGroup)
                ->color('warning'),
            Stat::make('Exercises', $exercises)
                ->description('In library')
                ->descriptionIcon(Heroicon::Fire)
                ->color('gray'),
        ];
    }
}
