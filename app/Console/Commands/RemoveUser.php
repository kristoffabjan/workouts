<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;
use function Laravel\Prompts\warning;

class RemoveUser extends Command
{
    protected $signature = 'user:remove
                            {user? : User ID or email address}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Remove a user from the system';

    public function handle(): int
    {
        $userIdentifier = $this->argument('user');

        if (! $userIdentifier) {
            $userIdentifier = search(
                label: 'Search for user to remove',
                options: fn (string $value) => strlen($value) > 0
                    ? User::query()
                        ->where('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->limit(10)
                        ->get()
                        ->mapWithKeys(fn (User $user) => [$user->id => "{$user->name} ({$user->email})"])
                        ->all()
                    : [],
                placeholder: 'Start typing to search...',
                scroll: 10
            );
        }

        $user = $this->findUser($userIdentifier);

        if (! $user) {
            $this->components->error('User not found.');

            return self::FAILURE;
        }

        $this->displayUserSummary($user);

        if (! $this->option('force')) {
            $confirmed = confirm(
                label: "Are you sure you want to remove {$user->name}?",
                default: false,
                hint: 'This action cannot be undone.'
            );

            if (! $confirmed) {
                info('User removal cancelled.');

                return self::SUCCESS;
            }
        }

        $this->removeUser($user);

        $this->components->success("User '{$user->name}' has been removed successfully.");

        return self::SUCCESS;
    }

    protected function findUser(string $identifier): ?User
    {
        if (is_numeric($identifier)) {
            return User::find($identifier);
        }

        return User::where('email', $identifier)->first();
    }

    protected function displayUserSummary(User $user): void
    {
        $teamsCount = $user->teams()->count();
        $personalTeam = $user->personalTeam();
        $createdExercisesCount = $user->createdExercises()->count();
        $createdTrainingsCount = $user->createdTrainings()->count();
        $assignedTrainingsCount = $user->assignedTrainings()->count();

        note('User Details:');

        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $user->id],
                ['Name', $user->name],
                ['Email', $user->email],
                ['Admin', $user->is_admin ? 'Yes' : 'No'],
                ['Teams', $teamsCount],
                ['Personal Team', $personalTeam ? $personalTeam->name : 'None'],
                ['Created Exercises', $createdExercisesCount],
                ['Created Trainings', $createdTrainingsCount],
                ['Assigned Trainings', $assignedTrainingsCount],
            ]
        );

        if ($personalTeam) {
            warning("Personal team '{$personalTeam->name}' will be deleted.");
        }

        if ($createdExercisesCount > 0) {
            warning("{$createdExercisesCount} exercise(s) will be deleted.");
        }

        if ($createdTrainingsCount > 0) {
            warning("{$createdTrainingsCount} training(s) will be deleted.");
        }
    }

    protected function removeUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $personalTeam = $user->personalTeam();

            if ($personalTeam) {
                $personalTeam->exercises()->delete();
                $personalTeam->trainings()->each(function ($training) {
                    $training->users()->detach();
                    $training->delete();
                });
                $personalTeam->users()->detach();
                $personalTeam->delete();
            }

            $user->createdExercises()->delete();

            $user->createdTrainings()->each(function ($training) {
                $training->users()->detach();
                $training->delete();
            });

            $user->assignedTrainings()->detach();
            $user->teams()->detach();

            $user->delete();
        });
    }
}
