<?php

namespace App\Filament\App\Resources\Trainings\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExercisesRelationManager extends RelationManager
{
    protected static string $relationship = 'exercises';

    protected static ?string $title = 'Exercises';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->allowDuplicates()
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->wrap(),
            ])
            ->reorderable('sort_order')
            ->afterReordering(function (array $order): void {
                foreach ($order as $index => $recordKey) {
                    $this->getOwnerRecord()->exercises()->updateExistingPivot(
                        $recordKey,
                        ['sort_order' => $index]
                    );
                }
            })
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('exercise_training.sort_order', 'asc'))
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $team = Filament::getTenant();
                        if ($team) {
                            $query->where('team_id', $team->id);
                        }

                        return $query;
                    })
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Exercise')
                            ->searchable()
                            ->preload(),
                        Textarea::make('notes')
                            ->rows(2)
                            ->placeholder('Optional notes for this exercise'),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['sort_order'] = $this->getOwnerRecord()->exercises()->count();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        $data['notes'] = $record->pivot->notes;

                        return $data;
                    })
                    ->using(function ($record, array $data): void {
                        $this->getOwnerRecord()->exercises()->updateExistingPivot(
                            $record->id,
                            ['notes' => $data['notes']]
                        );
                    }),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
