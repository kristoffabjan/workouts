<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Enums\TeamRole;
use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register new team';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', str()->slug($state ?? ''))),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $data['is_personal'] = false;
        $data['owner_id'] = auth()->id();

        $team = Team::create($data);

        $team->users()->attach(auth()->user(), ['role' => TeamRole::Coach->value]);

        return $team;
    }
}
