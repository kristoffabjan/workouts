<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        Team::create([
            'name' => 'Olympic Weightlifting Club',
            'slug' => 'olympic-weightlifting-club',
        ]);

        Team::create([
            'name' => 'Running Academy',
            'slug' => 'running-academy',
        ]);

        Team::create([
            'name' => 'CrossFit Box',
            'slug' => 'crossfit-box',
        ]);

        Team::create([
            'name' => 'Personal Training Studio',
            'slug' => 'personal-training-studio',
        ]);

        Team::factory()->count(3)->create();
    }
}
