<?php

namespace App\Filament\App\Resources\Users\Pages;

use App\Filament\App\Resources\Users\UserResource;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Schema $schema): Schema
    {
        $tenant = Filament::getTenant();

        return $schema
            ->components([
                Section::make('User Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('team_role')
                            ->label('Role in Team')
                            ->state(function ($record) use ($tenant) {
                                $role = $record->getRoleInTeam($tenant);

                                return $role?->getLabel() ?? 'No role';
                            })
                            ->badge()
                            ->color(function ($record) use ($tenant) {
                                $role = $record->getRoleInTeam($tenant);

                                return $role?->getColor() ?? 'gray';
                            })
                            ->icon(function ($record) use ($tenant) {
                                $role = $record->getRoleInTeam($tenant);

                                return $role?->getIcon();
                            }),
                        TextEntry::make('created_at')
                            ->label('Member Since')
                            ->dateTime('M j, Y'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
