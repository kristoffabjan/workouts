<?php

namespace App\Filament\App\Resources\Users\RelationManagers;

use App\Enums\TrainingStatus;
use App\Filament\App\Resources\Trainings\TrainingResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssignedTrainingsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedTrainings';

    protected static ?string $title = 'Assigned Trainings';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        if (! $user || ! $team) {
            return false;
        }

        return $user->isCoach($team);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (TrainingStatus $state): string => $state->getLabel())
                    ->icon(fn (TrainingStatus $state): mixed => $state->getIcon())
                    ->color(fn (TrainingStatus $state): array => $state->getColor())
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('Not scheduled'),
                TextColumn::make('exercises_count')
                    ->label('Exercises')
                    ->counts('exercises')
                    ->sortable(),
                IconColumn::make('pivot.completed_at')
                    ->label('Completed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                TextColumn::make('pivot.completed_at')
                    ->label('Completed At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TrainingStatus::class),
                Filter::make('scheduled_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From '.$data['from'];
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until '.$data['until'];
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => TrainingResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->emptyStateHeading('No trainings assigned')
            ->emptyStateDescription('This user has no trainings assigned to them.');
    }
}
