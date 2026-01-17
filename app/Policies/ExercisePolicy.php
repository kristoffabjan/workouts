<?php

namespace App\Policies;

use App\Models\Exercise;
use App\Models\User;
use Filament\Facades\Filament;

class ExercisePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return Filament::getTenant() !== null;
    }

    public function view(User $user, Exercise $exercise): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $exercise->team_id === $team->id;
    }

    public function create(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $user->isCoach($team);
    }

    public function update(User $user, Exercise $exercise): bool
    {
        if ($user->is_admin && $exercise->team_id === null) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $exercise->team_id === $team->id && $user->isCoach($team);
    }

    public function delete(User $user, Exercise $exercise): bool
    {
        if ($user->is_admin && $exercise->team_id === null) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $exercise->team_id === $team->id && $user->isCoach($team);
    }

    public function deleteAny(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $user->isCoach($team);
    }

    public function restore(User $user, Exercise $exercise): bool
    {
        return $this->delete($user, $exercise);
    }

    public function forceDelete(User $user, Exercise $exercise): bool
    {
        return $this->delete($user, $exercise);
    }
}
