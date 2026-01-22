<?php

namespace App\Filament\Admin\Resources\Trainings\Schemas;

use App\Enums\TrainingStatus;
use App\Models\Team;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('team_id')
                    ->label('Team')
                    ->options(Team::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('created_by')
                    ->label('Created By')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                RichEditor::make('content')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(TrainingStatus::class)
                    ->default(TrainingStatus::Draft)
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled At'),
            ]);
    }
}
