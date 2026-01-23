<?php

namespace App\Filament\App\Resources\Trainings\Schemas;

use App\Enums\TrainingStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Enum;

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
                    ->required()
                    ->rule(new Enum(TrainingStatus::class))
                    ->live(),
                DateTimePicker::make('scheduled_at')
                    ->label(__('trainings.fields.scheduled_at'))
                    ->native(false)
                    ->minDate(now())
                    ->required(fn (Get $get): bool => $get('status') === TrainingStatus::Scheduled->value)
                    ->rules([
                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            if ($get('status') === TrainingStatus::Scheduled->value && empty($value)) {
                                $fail(__('trainings.validation.scheduled_date_required'));
                            }
                            if ($value && now()->isAfter($value)) {
                                $fail(__('trainings.validation.scheduled_date_in_past'));
                            }
                        },
                    ]),
            ]);
    }
}
