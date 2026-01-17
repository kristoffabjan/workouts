<?php

namespace App\Filament\App\Resources\Exercises\Pages;

use App\Filament\App\Resources\Exercises\ExerciseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExercise extends CreateRecord
{
    protected static string $resource = ExerciseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
