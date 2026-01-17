<?php

namespace App\Filament\Admin\Resources\Exercises\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExercisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tags')
                    ->badge()
                    ->separator(',')
                    ->searchable(),
                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable()
                    ->default('System'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tags')
                    ->options(self::getTagOptions())
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn ($query, array $data) => self::filterByTags($query, $data['values'] ?? [])),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    private static function getTagOptions(): array
    {
        return [
            'strength' => 'Strength',
            'cardio' => 'Cardio',
            'flexibility' => 'Flexibility',
            'mobility' => 'Mobility',
            'core' => 'Core',
            'upper-body' => 'Upper Body',
            'lower-body' => 'Lower Body',
            'full-body' => 'Full Body',
            'compound' => 'Compound',
            'isolation' => 'Isolation',
            'olympic' => 'Olympic',
            'plyometric' => 'Plyometric',
            'bodyweight' => 'Bodyweight',
            'machine' => 'Machine',
            'dumbbell' => 'Dumbbell',
            'barbell' => 'Barbell',
            'kettlebell' => 'Kettlebell',
            'unilateral' => 'Unilateral',
            'bilateral' => 'Bilateral',
            'push' => 'Push',
            'pull' => 'Pull',
            'endurance' => 'Endurance',
            'power' => 'Power',
        ];
    }

    private static function filterByTags($query, array $tags): void
    {
        if (empty($tags)) {
            return;
        }

        foreach ($tags as $tag) {
            $query->whereJsonContains('tags', $tag);
        }
    }
}
