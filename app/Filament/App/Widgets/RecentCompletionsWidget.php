<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Trainings\TrainingResource;
use App\Models\TrainingUser;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentCompletionsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        return $user && $team && $user->isCoach($team);
    }

    public function getTableHeading(): string
    {
        return 'Recent Client Completions';
    }

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->query(
                TrainingUser::query()
                    ->whereHas('training', fn ($q) => $q->where('team_id', $team->id))
                    ->whereNotNull('completed_at')
                    ->with(['training', 'user'])
                    ->orderByDesc('completed_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable(),
                TextColumn::make('training.title')
                    ->label('Training')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('feedback')
                    ->label('Feedback')
                    ->limit(40)
                    ->placeholder('No feedback')
                    ->wrap(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (TrainingUser $record) => TrainingResource::getUrl('view', [
                        'record' => $record->training_id,
                        'tenant' => Filament::getTenant(),
                    ])),
            ])
            ->emptyStateHeading('No completions yet')
            ->emptyStateDescription('Client training completions will appear here.')
            ->emptyStateIcon(Heroicon::OutlinedClipboardDocumentCheck)
            ->paginated(false);
    }
}
