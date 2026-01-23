<?php

namespace App\Filament\App\Widgets;

use App\Models\Training;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        return $user && $team && $user->isClient($team);
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        $completedThisWeek = Training::query()
            ->where('team_id', $team->id)
            ->whereHas('assignedUsers', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->where('completed_at', '>=', now()->startOfWeek());
            })
            ->count();

        $completedThisMonth = Training::query()
            ->where('team_id', $team->id)
            ->whereHas('assignedUsers', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->where('completed_at', '>=', now()->startOfMonth());
            })
            ->count();

        $completedThisYear = Training::query()
            ->where('team_id', $team->id)
            ->whereHas('assignedUsers', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->where('completed_at', '>=', now()->startOfYear());
            })
            ->count();

        $totalAssigned = Training::query()
            ->where('team_id', $team->id)
            ->whereHas('assignedUsers', fn ($q) => $q->where('user_id', $user->id))
            ->count();

        return [
            Stat::make('This Week', $completedThisWeek)
                ->description('Trainings completed')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('success'),
            Stat::make('This Month', $completedThisMonth)
                ->description('Trainings completed')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('info'),
            Stat::make('This Year', $completedThisYear)
                ->description('Trainings completed')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('warning'),
            Stat::make('Total Assigned', $totalAssigned)
                ->description('All time')
                ->descriptionIcon(Heroicon::Calendar)
                ->color('gray'),
        ];
    }
}
