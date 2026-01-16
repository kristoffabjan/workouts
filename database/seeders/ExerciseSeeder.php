<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            $coach = $team->coaches()->first() ?? $team->admins()->first();

            if (! $coach) {
                continue;
            }

            Exercise::factory()
                ->count(10)
                ->forTeam($team)
                ->createdBy($coach)
                ->create();
        }
    }
}
