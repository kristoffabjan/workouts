<?php

namespace App\Filament\App\Resources\Trainings\Schemas;

use App\Enums\TrainingStatus;
use Filament\Forms\Components\DatePicker;
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
                RichEditor::make('content')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(TrainingStatus::class)
                    ->default(TrainingStatus::Draft)
                    ->required(),
                DatePicker::make('scheduled_date')
                    ->label('Scheduled Date'),
            ]);
    }
}
