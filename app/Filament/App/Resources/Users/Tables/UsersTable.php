<?php

namespace App\Filament\App\Resources\Users\Tables;

use App\Enums\TeamRole;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
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
                    ->sortable()
                    ->description(fn (User $record): ?string => self::isTeamOwner($record) ? 'Team Owner' : null),
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
                Action::make('remove')
                    ->label('Remove')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->visible(fn (User $record): bool => self::canRemoveUser($record))
                    ->requiresConfirmation()
                    ->modalHeading('Remove from Team')
                    ->modalDescription(fn (User $record): string => "Are you sure you want to remove {$record->name} from this team?")
                    ->action(function (User $record): void {
                        $tenant = Filament::getTenant();
                        $record->teams()->detach($tenant);

                        Notification::make()
                            ->success()
                            ->title('User removed')
                            ->body("{$record->name} has been removed from the team.")
                            ->send();
                    }),
            ])
            ->defaultSort('name');
    }

    private static function isTeamOwner(User $record): bool
    {
        $tenant = Filament::getTenant();

        return $tenant?->owner_id === $record->id;
    }

    private static function canRemoveUser(User $record): bool
    {
        $tenant = Filament::getTenant();
        $currentUser = auth()->user();

        if ($tenant?->is_personal) {
            return false;
        }

        if ($tenant?->owner_id !== $currentUser->id) {
            return false;
        }

        if ($record->id === $currentUser->id) {
            return false;
        }

        if ($tenant?->owner_id === $record->id) {
            return false;
        }

        return true;
    }
}
