<?php

namespace App\Filament\App\Resources\Exercises\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExerciseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('exercises.fields.name'))
                    ->required()
                    ->maxLength(255),
                RichEditor::make('description')
                    ->label(__('exercises.fields.description'))
                    ->columnSpanFull(),
                Repeater::make('video_urls')
                    ->simple(
                        TextInput::make('url')
                            ->url()
                            ->placeholder(__('exercises.placeholders.video_url'))
                    )
                    ->label(__('exercises.fields.video_urls'))
                    ->addActionLabel(__('exercises.actions.add_video_url'))
                    ->columnSpanFull(),
                TagsInput::make('tags')
                    ->label(__('exercises.fields.tags'))
                    ->placeholder(__('exercises.placeholders.tags'))
                    ->suggestions([
                        'strength',
                        'cardio',
                        'flexibility',
                        'mobility',
                        'core',
                        'upper-body',
                        'lower-body',
                        'full-body',
                        'compound',
                        'isolation',
                        'olympic',
                        'plyometric',
                        'bodyweight',
                        'machine',
                        'dumbbell',
                        'barbell',
                        'kettlebell',
                        'unilateral',
                        'bilateral',
                        'push',
                        'pull',
                        'endurance',
                        'power',
                    ]),
            ]);
    }
}
