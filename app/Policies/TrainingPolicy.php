<?php

namespace App\Policies;

use App\Models\Training;
use App\Models\User;
use Filament\Facades\Filament;

class TrainingPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return Filament::getTenant() !== null;
    }

    public function view(User $user, Training $training): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $team = Filament::getTenant();

        if (! $team || $training->team_id !== $team->id) {
            return false;
        }

        if ($user->isCoach($team)) {
            return true;
        }

        return $training->assignedUsers()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $user->isCoach($team);
    }

    public function update(User $user, Training $training): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $training->team_id === $team->id && $user->isCoach($team);
    }

    public function delete(User $user, Training $training): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $training->team_id === $team->id && $user->isCoach($team);
    }

    public function deleteAny(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $team = Filament::getTenant();

        return $team && $user->isCoach($team);
    }

    public function restore(User $user, Training $training): bool
    {
        return $this->delete($user, $training);
    }

    public function forceDelete(User $user, Training $training): bool
    {
        return $this->delete($user, $training);
    }

    public function markComplete(User $user, Training $training): bool
    {
        if ($user->is_admin) {
            return false;
        }

        $team = Filament::getTenant();

        if (! $team || $training->team_id !== $team->id) {
            return false;
        }

        if ($user->isCoach($team)) {
            return false;
        }

        return $training->assignedUsers()->where('user_id', $user->id)->exists();
    }
}
