<?php

namespace App\Filament\Admin\Resources\Teams\RelationManagers;

use App\Enums\TeamRole;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Team Members';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('role')
                    ->options(TeamRole::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TeamRole::from($state)->getLabel())
                    ->color(fn (string $state): array => TeamRole::from($state)->getColor())
                    ->icon(fn (string $state): mixed => TeamRole::from($state)->getIcon()),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(TeamRole::class)
                    ->query(fn ($query, array $data) => $data['value']
                        ? $query->wherePivot('role', $data['value'])
                        : $query),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->options(TeamRole::class)
                            ->default(TeamRole::Client)
                            ->required(),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Select::make('role')
                            ->options(TeamRole::class)
                            ->required(),
                    ])
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        $data['role'] = $record->pivot->role;

                        return $data;
                    })
                    ->using(function ($record, array $data): void {
                        $record->teams()->updateExistingPivot(
                            $this->getOwnerRecord()->id,
                            ['role' => $data['role']]
                        );
                    }),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
