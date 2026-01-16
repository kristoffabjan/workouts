<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Training;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    public function run(): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            $coach = $team->coaches()->first() ?? $team->admins()->first();
            $clients = $team->clients()->get();

            if (! $coach) {
                continue;
            }

            Training::factory()
                ->count(5)
                ->draft()
                ->forTeam($team)
                ->createdBy($coach)
                ->withExercises(3)
                ->create();

            Training::factory()
                ->count(10)
                ->scheduled()
                ->forTeam($team)
                ->createdBy($coach)
                ->withExercises(4)
                ->afterCreating(function ($training) use ($clients) {
                    if ($clients->isNotEmpty()) {
                        $assignedClients = $clients->random(min(2, $clients->count()));
                        foreach ($assignedClients as $client) {
                            $training->assignedUsers()->attach($client->id);
                        }
                    }
                })
                ->create();

            Training::factory()
                ->count(5)
                ->completed()
                ->forTeam($team)
                ->createdBy($coach)
                ->withExercises(4)
                ->afterCreating(function ($training) use ($clients) {
                    if ($clients->isNotEmpty()) {
                        $assignedClients = $clients->random(min(2, $clients->count()));
                        foreach ($assignedClients as $client) {
                            $training->assignedUsers()->attach($client->id, [
                                'completed_at' => now()->subDays(rand(1, 7)),
                                'feedback' => fake()->optional(0.5)->sentence(),
                            ]);
                        }
                    }
                })
                ->create();
        }
    }
}
