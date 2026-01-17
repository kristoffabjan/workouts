<?php

namespace App\Filament\App\Resources\Trainings\Pages;

use App\Filament\App\Resources\Trainings\TrainingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTraining extends CreateRecord
{
    protected static string $resource = TrainingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
