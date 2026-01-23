<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Trainings\TrainingResource;
use App\Models\Training;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingTrainingsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        return $user && $team && $user->isClient($team);
    }

    public function getTableHeading(): string
    {
        return 'Upcoming Trainings';
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $daysAhead = config('workouts.upcoming_days', 3);

        return $table
            ->query(
                Training::query()
                    ->whereHas('assignedUsers', fn ($q) => $q->where('user_id', $user->id))
                    ->where('scheduled_at', '>=', now())
                    ->where('scheduled_at', '<=', now()->addDays($daysAhead))
                    ->orderBy('scheduled_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('D, M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('exercises_count')
                    ->label('Exercises')
                    ->counts('exercises'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Training $record) => TrainingResource::getUrl('view', [
                        'record' => $record,
                        'tenant' => Filament::getTenant(),
                    ])),
            ])
            ->emptyStateHeading('No upcoming trainings')
            ->emptyStateDescription('You have no trainings scheduled in the next '.$daysAhead.' days.')
            ->emptyStateIcon(Heroicon::OutlinedCalendar)
            ->paginated(false);
    }
}
