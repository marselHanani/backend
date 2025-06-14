<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostJob>
 */
class PostJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'title' => fake()->jobTitle(),
            'company' => fake()->company(),
            'location' => fake()->city() . ', ' . fake()->stateAbbr(),
            'type' => fake()->randomElement(['Full-time', 'Part-time']),
            'salary' => '$' . number_format(fake()->numberBetween(40000, 60000)) . ' - $' . number_format(fake()->numberBetween(61000, 90000)),
            'description' => fake()->sentence(10),
            'requirements' => implode("\n", fake()->sentences(4)),
            'posted_date' => fake()->date('Y-m-d', '-1 month'),
            'deadline' => fake()->date('Y-m-d', '+1 month'),
            'category' => fake()->randomElement(['Design', 'Development', 'Marketing', 'Writing', 'Finance']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}