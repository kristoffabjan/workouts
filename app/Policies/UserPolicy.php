<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $tenant = Filament::getTenant();

        return $tenant && $user->isCoach($tenant);
    }

    public function view(User $user, User $model): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $tenant = Filament::getTenant();

        return $tenant && $user->isCoach($tenant) && $this->userBelongsToTeam($model, $tenant);
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, User $model): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $tenant = Filament::getTenant();

        return $tenant && $user->isCoach($tenant) && $this->userBelongsToTeam($model, $tenant);
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($this->hasCreatedContent($model)) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        $tenant = Filament::getTenant();

        return $tenant
            && $user->isCoach($tenant)
            && $this->userBelongsToTeam($model, $tenant);
    }

    public function restore(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    public function forceDelete(User $user, User $model): bool
    {
        if ($this->hasCreatedContent($model)) {
            return false;
        }

        return $user->is_admin;
    }

    private function userBelongsToTeam(User $user, Team $team): bool
    {
        return $user->teams()->where('team_id', $team->id)->exists();
    }

    private function hasCreatedContent(User $user): bool
    {
        return $user->createdTrainings()->exists() || $user->createdExercises()->exists();
    }
}
