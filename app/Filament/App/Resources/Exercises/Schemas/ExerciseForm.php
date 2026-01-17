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
                    ->required()
                    ->maxLength(255),
                RichEditor::make('description')
                    ->columnSpanFull(),
                Repeater::make('video_urls')
                    ->simple(
                        TextInput::make('url')
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...')
                    )
                    ->label('Video URLs')
                    ->addActionLabel('Add Video URL')
                    ->columnSpanFull(),
                TagsInput::make('tags')
                    ->placeholder('Add tags...')
                    ->suggestions([
                        'strength',
                        'cardio',
                        'flexibility',
                        'core',
                        'upper body',
                        'lower body',
                        'full body',
                    ]),
            ]);
    }
}
