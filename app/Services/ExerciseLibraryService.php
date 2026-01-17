<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

class ExerciseLibraryService
{
    public function copyToTeam(array $exerciseIds, Team $team, User $copiedBy): int
    {
        $globalExercises = Exercise::withoutGlobalScopes()
            ->global()
            ->whereIn('id', $exerciseIds)
            ->get();

        $existingNames = Exercise::withoutGlobalScopes()
            ->where('team_id', $team->id)
            ->pluck('name')
            ->map(fn ($name) => strtolower($name))
            ->toArray();

        $copiedCount = 0;

        foreach ($globalExercises as $exercise) {
            if (in_array(strtolower($exercise->name), $existingNames)) {
                continue;
            }

            Exercise::withoutGlobalScopes()->create([
                'team_id' => $team->id,
                'name' => $exercise->name,
                'description' => $exercise->description,
                'video_urls' => $exercise->video_urls,
                'tags' => $exercise->tags,
                'created_by' => $copiedBy->id,
            ]);

            $copiedCount++;
        }

        return $copiedCount;
    }

    public function getGlobalExercises(): Collection
    {
        return Exercise::withoutGlobalScopes()
            ->global()
            ->orderBy('name')
            ->get();
    }

    public function searchGlobalExercises(?string $search): Collection
    {
        $query = Exercise::withoutGlobalScopes()->global();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereJsonContains('tags', strtolower($search));
            });
        }

        return $query->orderBy('name')->get();
    }
}
