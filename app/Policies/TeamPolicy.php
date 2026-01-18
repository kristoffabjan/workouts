<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(User $user, Team $team): bool
    {
        return (bool) $user->is_admin;
    }

    public function create(User $user): bool
    {
        // All authenticated users can create teams (for tenant registration)
        return true;
    }

    public function update(User $user, Team $team): bool
    {
        return Auth::id() === $team->owner_id || (bool) $user->is_admin;
    }

    public function delete(User $user, Team $team): bool
    {
        return Auth::id() === $team->owner_id || (bool) $user->is_admin;
    }

    public function restore(User $user, Team $team): bool
    {
        return (bool) $user->is_admin;
    }

    public function forceDelete(User $user, Team $team): bool
    {
        return (bool) $user->is_admin;
    }

    public function deleteAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }
}
