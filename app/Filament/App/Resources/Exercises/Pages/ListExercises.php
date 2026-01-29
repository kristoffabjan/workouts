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
                ->label(__('exercises.actions.add_from_library'))
                ->icon(Heroicon::OutlinedSquare2Stack)
                ->color('gray')
                ->form([
                    Select::make('exercises')
                        ->label(__('exercises.library.select_label'))
                        ->options(fn () => $this->getGlobalExerciseOptions())
                        ->searchable()
                        ->multiple()
                        ->required()
                        ->helperText(__('exercises.library.helper_text')),
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
                            ->title(__('exercises.library.added_title'))
                            ->body(__('exercises.library.added_body', ['count' => $copiedCount]))
                            ->send();
                    } else {
                        Notification::make()
                            ->warning()
                            ->title(__('exercises.library.none_added_title'))
                            ->body(__('exercises.library.none_added_body'))
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
