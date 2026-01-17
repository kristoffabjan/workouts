<?php

namespace App\Filament\App\Resources\Exercises\Pages;

use App\Filament\App\Resources\Exercises\ExerciseResource;
use App\Models\Exercise;
use App\Services\ExerciseLibraryService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListExercises extends ListRecords
{
    protected static string $resource = ExerciseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addFromLibrary')
                ->label('Add from Library')
                ->icon(Heroicon::OutlinedSquare2Stack)
                ->color('gray')
                ->form([
                    Select::make('exercises')
                        ->label('Select exercises from the global library')
                        ->options(fn () => $this->getGlobalExerciseOptions())
                        ->searchable()
                        ->multiple()
                        ->required()
                        ->helperText('Search by name or tag. Exercises with the same name already in your team will be skipped.'),
                ])
                ->action(function (array $data, ExerciseLibraryService $service) {
                    $team = Filament::getTenant();
                    $copiedCount = $service->copyToTeam(
                        $data['exercises'],
                        $team,
                        auth()->user()
                    );

                    if ($copiedCount > 0) {
                        Notification::make()
                            ->success()
                            ->title('Exercises added')
                            ->body("{$copiedCount} exercise(s) added to your team.")
                            ->send();
                    } else {
                        Notification::make()
                            ->warning()
                            ->title('No exercises added')
                            ->body('Selected exercises already exist in your team.')
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }

    private function getGlobalExerciseOptions(): array
    {
        return Exercise::withoutGlobalScopes()
            ->global()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Exercise $exercise) => [
                $exercise->id => $exercise->name,
            ])
            ->toArray();
    }
}
