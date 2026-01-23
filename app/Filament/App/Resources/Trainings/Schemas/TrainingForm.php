<?php

namespace App\Filament\App\Resources\Trainings\Schemas;

use App\Enums\TrainingStatus;
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
                    ->label(__('trainings.fields.title'))
                    ->required()
                    ->maxLength(255),
                RichEditor::make('content')
                    ->label(__('trainings.fields.content'))
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('trainings.fields.status'))
                    ->options(TrainingStatus::class)
                    ->default(TrainingStatus::Draft)
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label(__('trainings.fields.scheduled_at')),
            ]);
    }
}
