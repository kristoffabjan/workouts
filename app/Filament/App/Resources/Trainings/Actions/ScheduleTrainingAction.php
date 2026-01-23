<?php

namespace App\Filament\App\Resources\Trainings\Actions;

use App\Enums\TeamRole;
use App\Models\Training;
use App\Models\User;
use App\Services\BulkScheduleService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ScheduleTrainingAction
{
    public static function make(): Action
    {
        return Action::make('schedule')
            ->label(__('trainings.actions.schedule'))
            ->icon(Heroicon::OutlinedCalendar)
            ->color('success')
            ->schema([
                Tabs::make('schedule_method')
                    ->tabs([
                        Tab::make(__('trainings.schedule.single_date'))
                            ->icon(Heroicon::OutlinedCalendar)
                            ->schema([
                                DateTimePicker::make('single_date')
                                    ->label(__('trainings.schedule.schedule_date_time'))
                                    ->native(false)
                                    ->minDate(now())
                                    ->default(now()->addDay()->setHour(9)->setMinute(0)),
                            ]),
                        Tab::make(__('trainings.schedule.multiple_dates'))
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->schema([
                                Repeater::make('specific_dates')
                                    ->label(__('trainings.schedule.multiple_dates'))
                                    ->schema([
                                        DateTimePicker::make('date')
                                            ->native(false)
                                            ->minDate(now()),
                                    ])
                                    ->defaultItems(1)
                                    ->addActionLabel(__('app.actions.create'))
                                    ->reorderable(false)
                                    ->columns(1),
                            ]),
                        Tab::make(__('trainings.schedule.weekly_pattern'))
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->label(__('trainings.schedule.start_date_time'))
                                    ->native(false)
                                    ->minDate(now()),
                                TextInput::make('weeks')
                                    ->label(__('trainings.schedule.number_of_weeks'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(52),
                                Select::make('days_of_week')
                                    ->label(__('trainings.schedule.days_of_week'))
                                    ->multiple()
                                    ->options([
                                        0 => __('trainings.days.sunday'),
                                        1 => __('trainings.days.monday'),
                                        2 => __('trainings.days.tuesday'),
                                        3 => __('trainings.days.wednesday'),
                                        4 => __('trainings.days.thursday'),
                                        5 => __('trainings.days.friday'),
                                        6 => __('trainings.days.saturday'),
                                    ]),
                            ]),
                    ]),
                Section::make(__('trainings.schedule.options'))
                    ->schema([
                        Toggle::make('copy_exercises')
                            ->label(__('trainings.schedule.copy_exercises'))
                            ->default(true)
                            ->helperText(__('trainings.schedule.copy_exercises_description')),
                        Select::make('assign_to')
                            ->label(__('trainings.schedule.assign_to'))
                            ->multiple()
                            ->options(function () {
                                $team = Filament::getTenant();
                                if (! $team) {
                                    return [];
                                }

                                return User::whereHas('teams', function (Builder $query) use ($team) {
                                    $query->where('team_id', $team->id)
                                        ->where('role', TeamRole::Client->value);
                                })->pluck('name', 'id')->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(__('trainings.schedule.assign_to_placeholder')),
                    ])
                    ->collapsible(),
            ])
            ->action(function (array $data, Training $record) {
                $service = app(BulkScheduleService::class);

                $dates = self::resolveDates($data);

                if (empty($dates)) {
                    Notification::make()
                        ->title(__('app.messages.error'))
                        ->warning()
                        ->send();

                    return;
                }

                // Single date mode = update existing training (only when using single_date field and no weekly/multiple)
                $isSingleDateMode = count($dates) === 1
                    && ! empty($data['single_date'])
                    && empty($data['weeks'])
                    && empty(collect($data['specific_dates'] ?? [])->pluck('date')->filter()->toArray());

                if ($isSingleDateMode) {
                    $service->scheduleTraining(
                        $record,
                        $dates[0],
                        $data['assign_to'] ?? []
                    );

                    Notification::make()
                        ->title(__('trainings.notifications.scheduled'))
                        ->success()
                        ->send();

                    return;
                }

                // Multiple dates = duplicate training
                $createdCount = $service->duplicateToMultipleDates(
                    $record,
                    $dates,
                    $data['assign_to'] ?? [],
                    $data['copy_exercises'] ?? true
                );

                Notification::make()
                    ->title(__('trainings.notifications.created_count', ['count' => $createdCount]))
                    ->success()
                    ->send();
            })
            ->visible(function () {
                $team = Filament::getTenant();
                $user = auth()->user();

                return $team && $user && $user->isCoach($team);
            });
    }

    protected static function resolveDates(array $data): array
    {
        // Check for weekly pattern first (multiple dates)
        if (! empty($data['weeks']) && (int) $data['weeks'] > 0 && ! empty($data['start_date'])) {
            $service = app(BulkScheduleService::class);

            return $service->generateWeeklyDates(
                $data['start_date'],
                (int) $data['weeks'],
                $data['days_of_week'] ?? []
            );
        }

        // Check for specific dates (multiple dates)
        if (! empty($data['specific_dates'])) {
            $specificDates = collect($data['specific_dates'])
                ->pluck('date')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (! empty($specificDates)) {
                return $specificDates;
            }
        }

        // Fall back to single date
        if (! empty($data['single_date'])) {
            return [$data['single_date']];
        }

        return [];
    }

    public static function makeTableAction(): Action
    {
        return self::make();
    }
}
