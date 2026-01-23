<?php

namespace App\Services;

use App\Enums\TeamRole;
use App\Enums\TrainingStatus;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BulkScheduleService
{
    public function validateTeamMembership(array $userIds, ?Team $team): array
    {
        if (empty($userIds) || ! $team) {
            return [];
        }

        $validUserIds = User::whereIn('id', $userIds)
            ->whereHas('teams', function ($query) use ($team) {
                $query->where('team_id', $team->id)
                    ->where('role', TeamRole::Client->value);
            })
            ->pluck('id')
            ->toArray();

        return array_intersect($userIds, $validUserIds);
    }

    public function duplicateToMultipleDates(
        Training $training,
        array $dates,
        array $userIds = [],
        bool $copyExercises = true
    ): int {
        $createdCount = 0;

        DB::transaction(function () use ($training, $dates, $userIds, $copyExercises, &$createdCount) {
            $validUserIds = ! empty($userIds)
                ? $this->validateTeamMembership($userIds, $training->team)
                : [];

            foreach ($dates as $date) {
                $newTraining = $this->duplicateTraining($training, $date, $copyExercises);

                if (! empty($validUserIds)) {
                    $newTraining->assignedUsers()->attach($validUserIds);
                } elseif ($training->assignedUsers()->exists()) {
                    $newTraining->assignedUsers()->attach(
                        $training->assignedUsers()->pluck('user_id')->toArray()
                    );
                }

                $createdCount++;
            }
        });

        return $createdCount;
    }

    public function duplicateTraining(
        Training $training,
        \DateTimeInterface|string $scheduledAt,
        bool $copyExercises = true
    ): Training {
        $newTraining = $training->replicate([
            'created_at',
            'updated_at',
            'deleted_at',
            'assigned_users_count',
            'exercises_count',
        ]);

        $newTraining->scheduled_at = $scheduledAt;
        $newTraining->status = TrainingStatus::Scheduled;
        $newTraining->created_by = auth()->id();
        $newTraining->save();

        if ($copyExercises && $training->exercises()->exists()) {
            $this->copyExercises($training, $newTraining);
        }

        return $newTraining;
    }

    protected function copyExercises(Training $source, Training $target): void
    {
        $exercisesWithPivot = $source->exercises()->get();

        foreach ($exercisesWithPivot as $exercise) {
            $target->exercises()->attach($exercise->id, [
                'notes' => $exercise->pivot->notes,
                'sort_order' => $exercise->pivot->sort_order,
            ]);
        }
    }

    public function generateWeeklyDates(
        \DateTimeInterface|string $startDate,
        int $weeks,
        array $daysOfWeek = []
    ): array {
        $dates = [];
        $start = \Carbon\Carbon::parse($startDate);

        if (empty($daysOfWeek)) {
            for ($week = 0; $week < $weeks; $week++) {
                $date = $start->copy()->addWeeks($week);
                $dates[] = $date->format('Y-m-d H:i:s');
            }

            return $dates;
        }

        $startOfFirstWeek = $start->copy()->startOfWeek(\Carbon\Carbon::MONDAY);

        for ($week = 0; $week < $weeks; $week++) {
            foreach ($daysOfWeek as $dayOfWeek) {
                $date = $startOfFirstWeek->copy()
                    ->addWeeks($week)
                    ->addDays($dayOfWeek === 0 ? 6 : $dayOfWeek - 1);

                if ($date->gte($start)) {
                    $dates[] = $date->format('Y-m-d H:i:s');
                }
            }
        }

        sort($dates);

        return array_unique($dates);
    }

    public function scheduleTraining(
        Training $training,
        \DateTimeInterface|string $scheduledAt,
        array $userIds = []
    ): Training {
        $training->scheduled_at = $scheduledAt;
        $training->status = TrainingStatus::Scheduled;
        $training->save();

        if (! empty($userIds)) {
            $validUserIds = $this->validateTeamMembership($userIds, $training->team);
            $existingUserIds = $training->assignedUsers()->pluck('user_id')->toArray();
            $newUserIds = array_diff($validUserIds, $existingUserIds);

            if (! empty($newUserIds)) {
                $training->assignedUsers()->attach($newUserIds);
            }
        }

        return $training;
    }
}
