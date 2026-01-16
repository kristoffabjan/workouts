<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        $exercises = [
            // Olympic Weightlifting
            'Snatch', 'Clean & Jerk', 'Clean', 'Jerk', 'Power Snatch', 'Power Clean',
            'Hang Snatch', 'Hang Clean', 'Snatch Pull', 'Clean Pull',
            'Snatch Deadlift', 'Clean Deadlift', 'Snatch High Pull', 'Clean High Pull',
            'Overhead Squat', 'Snatch Balance', 'Drop Snatch', 'Muscle Snatch',
            'Push Press', 'Push Jerk', 'Split Jerk', 'Snatch Grip Deadlift',
            'Clean Grip Deadlift', 'Front Squat', 'Back Squat',
            // Running
            'Easy Run', 'Tempo Run', 'Interval Run', 'Long Run', 'Recovery Run',
            'Hill Sprints', 'Fartlek', '400m Repeats', '800m Repeats',
            'Mile Repeats', 'Sprint', 'Strides', 'Threshold Run',
            // General Strength
            'Deadlift', 'Bench Press', 'Overhead Press', 'Barbell Row',
            'Pull-up', 'Push-up', 'Lunge', 'Plank', 'Romanian Deadlift',
            'Bulgarian Split Squat', 'Hip Thrust', 'Leg Press',
            'Lat Pulldown', 'Cable Row', 'Dumbbell Curl', 'Tricep Extension',
            'Face Pull', 'Leg Curl', 'Leg Extension', 'Calf Raise',
            'Ab Crunch', 'Russian Twist', 'Good Morning', 'Box Jump',
        ];

        $tags = ['strength', 'olympic', 'running', 'cardio', 'compound', 'isolation', 'upper body', 'lower body', 'core', 'technique'];

        return [
            'team_id' => Team::factory(),
            'name' => fake()->randomElement($exercises),
            'description' => fake()->optional(0.7)->paragraph(),
            'video_urls' => fake()->optional(0.5)->randomElements([
                'https://www.youtube.com/watch?v=example1',
                'https://www.youtube.com/watch?v=example2',
            ], fake()->numberBetween(1, 2)),
            'tags' => fake()->randomElements($tags, fake()->numberBetween(1, 3)),
            'created_by' => User::factory(),
        ];
    }

    public function forTeam(Team $team): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team->id,
        ]);
    }

    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }

    public function olympic(): static
    {
        $exercises = ['Snatch', 'Clean & Jerk', 'Power Snatch', 'Power Clean', 'Hang Snatch', 'Hang Clean', 'Front Squat', 'Back Squat', 'Overhead Squat'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($exercises),
            'tags' => fake()->randomElements(['olympic', 'compound', 'technique', 'strength'], 2),
        ]);
    }

    public function running(): static
    {
        $exercises = ['Easy Run', 'Tempo Run', 'Interval Run', 'Long Run', 'Hill Sprints', 'Fartlek', 'Threshold Run'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($exercises),
            'tags' => fake()->randomElements(['running', 'cardio', 'endurance', 'speed'], 2),
        ]);
    }

    public function strength(): static
    {
        $exercises = ['Deadlift', 'Bench Press', 'Overhead Press', 'Barbell Row', 'Pull-up', 'Romanian Deadlift'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($exercises),
            'tags' => fake()->randomElements(['strength', 'compound', 'upper body', 'lower body'], 2),
        ]);
    }
}
