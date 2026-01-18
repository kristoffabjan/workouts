<?php

namespace App\Filament\App\Resources\Users\Schemas;

use App\Enums\TeamRole;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email address')
                    ->disabled(),
                Select::make('team_role')
                    ->label('Role in Team')
                    ->options(TeamRole::class)
                    ->default(fn ($record) => $record?->getRoleInTeam(Filament::getTenant())?->value)
                    ->disabled()
                    ->required(),
            ]);
    }
}
