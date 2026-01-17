<?php

namespace App\Observers;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    public function created(User $user): void
    {
        if ($user->is_admin) {
            return;
        }

        $this->createPersonalTeam($user);
    }

    private function createPersonalTeam(User $user): void
    {
        $baseName = $user->name."'s Team";
        $baseSlug = Str::slug($user->name);

        $slug = $baseSlug;
        $name = $baseName;
        $counter = 1;

        while (Team::where('slug', $slug)->orWhere('name', $name)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $name = $baseName.' ('.$counter.')';
            $counter++;
        }

        $team = Team::create([
            'name' => $name,
            'slug' => $slug,
            'is_personal' => true,
            'owner_id' => $user->id,
        ]);

        $user->teams()->attach($team, ['role' => TeamRole::Coach->value]);
    }
}
