<?php

namespace App\Filament\App\Resources\Users\Tables;

use App\Enums\TeamRole;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teams')
                    ->label('Role')
                    ->badge()
                    ->state(function ($record) {
                        $tenant = Filament::getTenant();
                        $role = $record->getRoleInTeam($tenant);

                        return $role?->getLabel();
                    })
                    ->color(function ($record) {
                        $tenant = Filament::getTenant();
                        $role = $record->getRoleInTeam($tenant);

                        return $role?->getColor();
                    })
                    ->icon(function ($record) {
                        $tenant = Filament::getTenant();
                        $role = $record->getRoleInTeam($tenant);

                        return $role?->getIcon();
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(TeamRole::class)
                    ->query(function ($query, array $data) {
                        if (! $data['value']) {
                            return $query;
                        }

                        $tenant = Filament::getTenant();

                        return $query->whereHas('teams', fn ($q) => $q
                            ->where('team_id', $tenant?->id)
                            ->where('role', $data['value']));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('Remove')
                    ->modalHeading('Remove from Team')
                    ->modalDescription('Are you sure you want to remove this user from the team?')
                    ->action(function ($record) {
                        $tenant = Filament::getTenant();
                        $record->teams()->detach($tenant);
                    }),
            ])
            ->defaultSort('name');
    }
}
