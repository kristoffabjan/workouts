<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Exercise;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTeams = Team::query()->count();
        $organizationTeams = Team::query()->where('is_personal', false)->count();
        $totalUsers = User::query()->where('is_admin', false)->count();
        $totalTrainings = Training::query()->count();
        $totalExercises = Exercise::query()->count();
        $globalExercises = Exercise::query()->whereNull('team_id')->count();

        return [
            Stat::make('Total Teams', $totalTeams)
                ->description($organizationTeams.' organizations')
                ->descriptionIcon(Heroicon::BuildingOffice)
                ->color('primary'),
            Stat::make('Total Users', $totalUsers)
                ->description('Coaches & clients')
                ->descriptionIcon(Heroicon::Users)
                ->color('info'),
            Stat::make('Total Trainings', $totalTrainings)
                ->description('Across all teams')
                ->descriptionIcon(Heroicon::ClipboardDocumentList)
                ->color('success'),
            Stat::make('Total Exercises', $totalExercises)
                ->description($globalExercises.' global exercises')
                ->descriptionIcon(Heroicon::Fire)
                ->color('warning'),
        ];
    }
}
