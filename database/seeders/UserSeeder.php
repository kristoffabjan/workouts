<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->globalAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $teams = Team::all();

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
