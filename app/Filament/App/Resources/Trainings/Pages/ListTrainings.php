<?php

namespace App\Filament\App\Resources\Trainings\Pages;

use App\Filament\App\Resources\Trainings\TrainingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainings extends ListRecords
{
    protected static string $resource = TrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
