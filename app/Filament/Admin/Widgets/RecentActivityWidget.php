<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Trainings\TrainingResource;
use App\Models\Training;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return 'Recent Trainings';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Training::query()
                    ->with(['team', 'creator'])
                    ->orderByDesc('created_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('team.name')
                    ->label('Team')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('creator.name')
                    ->label('Created By'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Training $record) => TrainingResource::getUrl('view', [
                        'record' => $record,
                    ])),
            ])
            ->emptyStateHeading('No trainings yet')
            ->emptyStateDescription('Trainings will appear here once created.')
            ->emptyStateIcon(Heroicon::OutlinedClipboardDocumentList)
            ->paginated(false);
    }
}
