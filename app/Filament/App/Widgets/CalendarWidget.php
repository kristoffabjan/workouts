<?php

namespace App\Filament\App\Widgets;

use App\Enums\TrainingStatus;
use App\Filament\App\Resources\Trainings\TrainingResource;
use App\Models\Training;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Training::class;

    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        return true;
    }

    public function fetchEvents(array $info): array
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        if (! $user || ! $team) {
            return [];
        }

        $query = Training::query()
            ->where('team_id', $team->id)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', $info['start'])
            ->where('scheduled_at', '<=', $info['end'])
            ->with(['assignedUsers']);

        if ($user->isClient($team)) {
            $query->whereHas('assignedUsers', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query->get()
            ->map(function (Training $training) use ($user, $team): EventData {
                $isCompleted = false;
                $isAssigned = false;

                if ($user->isClient($team)) {
                    $pivot = $training->assignedUsers
                        ->where('id', $user->id)
                        ->first()
                        ?->pivot;

                    $isCompleted = $pivot?->completed_at !== null;
                    $isAssigned = true;
                } else {
                    $isCompleted = $training->status === TrainingStatus::Completed;
                    $isAssigned = $training->assignedUsers->contains('id', $user->id);
                }

                return EventData::make()
                    ->id($training->id)
                    ->title($training->title)
                    ->start($training->scheduled_at)
                    ->allDay(true)
                    ->url(TrainingResource::getUrl('view', [
                        'record' => $training,
                        'tenant' => $team->slug,
                    ]))
                    ->backgroundColor($this->getEventColor($training->status, $isCompleted))
                    ->borderColor($this->getEventColor($training->status, $isCompleted))
                    ->extendedProps([
                        'status' => $training->status->value,
                        'isCompleted' => $isCompleted,
                        'isAssigned' => $isAssigned,
                        'assignedCount' => $training->assignedUsers->count(),
                    ]);
            })
            ->toArray();
    }

    protected function getEventColor(TrainingStatus $status, bool $isCompleted): string
    {
        if ($isCompleted) {
            return '#5AB58A'; // brand success
        }

        return match ($status) {
            TrainingStatus::Draft => 'rgb(156, 163, 175)', // gray-400
            TrainingStatus::Scheduled => '#FAAC68', // brand warning/orange
            TrainingStatus::Completed => '#5AB58A', // brand success
            TrainingStatus::Skipped, TrainingStatus::Missed => '#FA6868', // brand danger/red
        };
    }

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,listWeek',
            ],
            'navLinks' => true,
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'meridiem' => false,
                'hour12' => false,
            ],
            'nowIndicator' => true,
            'dayMaxEvents' => true,
        ];
    }

    protected function headerActions(): array
    {
        return [];
    }

    protected function modalActions(): array
    {
        return [];
    }
}
