<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Enums\TeamRole;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register new team';
    }

    public function hasLogo(): bool
    {
        return true;
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
        $data['owner_id'] = Auth::user()->id;

        $team = Team::create($data);

        $team->users()->attach(Auth::user(), ['role' => TeamRole::Coach->value]);

        return $team;
    }
}
