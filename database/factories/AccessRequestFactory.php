<?php

namespace Database\Factories;

use App\Enums\AccessRequestStatus;
use App\Models\AccessRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccessRequest>
 */
class AccessRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'message' => fake()->optional()->paragraph(),
            'status' => AccessRequestStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccessRequestStatus::Approved,
            'processed_at' => now(),
            'processed_by' => User::factory(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccessRequestStatus::Rejected,
            'processed_at' => now(),
            'processed_by' => User::factory(),
        ]);
    }
}
