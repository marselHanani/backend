<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobUser>
 */
class JobUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 10), // Adjust the range based on your user data
            'post_job_id' => fake()->numberBetween(1, 10), // Adjust the range based on your job dat
        ];
    }
}
