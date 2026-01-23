<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Trainings\TrainingResource;
use App\Models\Training;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingFeedbackWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        return $user && $team && $user->isClient($team);
    }

    public function getTableHeading(): string
    {
        return 'Pending Feedback';
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query(
                Training::query()
                    ->whereHas('assignedUsers', function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->whereNull('completed_at');
                    })
                    ->where('scheduled_at', '<', now())
                    ->orderBy('scheduled_at', 'desc')
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('D, M j, Y')
                    ->sortable(),
                TextColumn::make('exercises_count')
                    ->label('Exercises')
                    ->counts('exercises'),
            ])
            ->recordActions([
                Action::make('complete')
                    ->label('Mark Complete')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->url(fn (Training $record) => TrainingResource::getUrl('view', [
                        'record' => $record,
                        'tenant' => Filament::getTenant(),
                    ])),
            ])
            ->emptyStateHeading('All caught up!')
            ->emptyStateDescription('You have no trainings awaiting feedback.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle)
            ->paginated(false);
    }
}
