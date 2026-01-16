<?php

namespace Database\Seeders;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $teams = Team::all();

        $superAdmin = User::factory()->globalAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        foreach ($teams as $team) {
            $superAdmin->teams()->attach($team, ['role' => TeamRole::Admin]);
        }

        $multiTeamUser = User::factory()->create([
            'name' => 'Multi Team User',
            'email' => 'multiteam@example.com',
            'password' => 'password',
        ]);

        $teamArray = $teams->values()->all();
        if (count($teamArray) >= 3) {
            $multiTeamUser->teams()->attach($teamArray[0], ['role' => TeamRole::Admin]);
            $multiTeamUser->teams()->attach($teamArray[1], ['role' => TeamRole::Coach]);
            $multiTeamUser->teams()->attach($teamArray[2], ['role' => TeamRole::Client]);
        }

        foreach ($teams as $team) {
            User::factory()
                ->admin($team)
                ->create(['email' => "admin-{$team->slug}@example.com"]);

            User::factory()
                ->count(2)
                ->coach($team)
                ->create();

            User::factory()
                ->count(5)
                ->client($team)
                ->create();
        }
    }
}
