<?php

namespace App\Console\Commands;

use App\Enums\TrainingStatus;
use App\Models\Training;
use Illuminate\Console\Command;

class MarkMissedTrainings extends Command
{
    protected $signature = 'trainings:mark-missed';

    protected $description = 'Mark scheduled trainings as missed if not completed within the deadline';

    public function handle(): int
    {
        $deadlineDays = config('workouts.missed_deadline_days', 3);
        $cutoffDate = now()->subDays($deadlineDays)->startOfDay();

        $count = Training::query()
            ->where('status', TrainingStatus::Scheduled)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<', $cutoffDate)
            ->whereDoesntHave('assignedUsers', function ($query) {
                $query->whereNotNull('training_user.completed_at');
            })
            ->update(['status' => TrainingStatus::Missed]);

        if ($count === 0) {
            $this->info('No trainings to mark as missed.');
        } else {
            $this->info("Marked {$count} training(s) as missed.");
        }

        return self::SUCCESS;
    }
}
