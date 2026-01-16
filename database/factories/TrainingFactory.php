<?php

namespace Database\Factories;

use App\Enums\TrainingStatus;
use App\Models\Exercise;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Training>
 */
class TrainingFactory extends Factory
{
    public function definition(): array
    {
        $titles = [
            'Morning Workout', 'Strength Session', 'Technique Work', 'Recovery Day',
            'Heavy Singles', 'Volume Day', 'Speed Work', 'Conditioning',
            'Snatch Focus', 'Clean & Jerk Focus', 'Squat Day', 'Pull Day',
            'Easy Run Day', 'Tempo Training', 'Interval Session', 'Long Run',
        ];

        return [
            'team_id' => Team::factory(),
            'title' => fake()->randomElement($titles),
            'content' => fake()->optional(0.7)->paragraphs(2, true),
            'status' => TrainingStatus::Draft,
            'scheduled_date' => null,
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

    public function scheduled(?string $date = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TrainingStatus::Scheduled,
            'scheduled_date' => $date ?? fake()->dateTimeBetween('now', '+30 days'),
        ]);
    }

    public function completed(?string $date = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TrainingStatus::Completed,
            'scheduled_date' => $date ?? fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function skipped(?string $date = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TrainingStatus::Skipped,
            'scheduled_date' => $date ?? fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TrainingStatus::Draft,
            'scheduled_date' => null,
        ]);
    }

    public function withExercises(int $count = 3): static
    {
        return $this->afterCreating(function ($training) use ($count) {
            $exercises = Exercise::where('team_id', $training->team_id)->inRandomOrder()->limit($count)->get();

            if ($exercises->isEmpty()) {
                $exercises = Exercise::factory()->count($count)->forTeam($training->team)->createdBy($training->creator)->create();
            }

            $sortOrder = 0;
            foreach ($exercises as $exercise) {
                $training->exercises()->attach($exercise->id, [
                    'notes' => fake()->optional(0.3)->sentence(),
                    'sort_order' => $sortOrder++,
                ]);
            }
        });
    }

    public function assignedTo(User|array $users): static
    {
        return $this->afterCreating(function ($training) use ($users) {
            $users = is_array($users) ? $users : [$users];

            foreach ($users as $user) {
                $training->assignedUsers()->attach($user->id);
            }
        });
    }
}
