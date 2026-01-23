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

class RecentFeedbackWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        return $user && $team && $user->isClient($team);
    }

    public function getTableHeading(): string
    {
        return 'Your Recent Feedback';
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query(
                Training::query()
                    ->whereHas('assignedUsers', function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->whereNotNull('completed_at')
                            ->whereNotNull('feedback');
                    })
                    ->orderByDesc(
                        fn ($q) => $q->selectRaw('MAX(completed_at)')
                            ->from('training_user')
                            ->whereColumn('training_user.training_id', 'trainings.id')
                            ->where('user_id', $user->id)
                    )
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('assignedUsers')
                    ->label('Completed')
                    ->formatStateUsing(function (Training $record) use ($user) {
                        $pivot = $record->assignedUsers->firstWhere('id', $user->id)?->pivot;

                        return $pivot?->completed_at?->diffForHumans();
                    }),
                TextColumn::make('feedback')
                    ->label('Feedback')
                    ->formatStateUsing(function (Training $record) use ($user) {
                        $pivot = $record->assignedUsers->firstWhere('id', $user->id)?->pivot;

                        return str($pivot?->feedback ?? '')->limit(50);
                    })
                    ->wrap(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Training $record) => TrainingResource::getUrl('view', [
                        'record' => $record,
                        'tenant' => Filament::getTenant(),
                    ])),
            ])
            ->emptyStateHeading('No feedback yet')
            ->emptyStateDescription('Complete a training and submit feedback to see it here.')
            ->emptyStateIcon(Heroicon::OutlinedChatBubbleLeft)
            ->paginated(false);
    }
}
