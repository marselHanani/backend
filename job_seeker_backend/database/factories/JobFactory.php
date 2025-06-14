<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostJob>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' =>fake()->jobTitle(),
            'description' =>fake()->paragraph(3),
            'experience' =>fake()->numberBetween(0, 10),
            'requirements' =>fake()->sentence(8),
            'responsibilities' =>fake()->sentence(10),
            'education' =>fake()->randomElement(['Bachelor', 'Master', 'PhD']),
            'vacancies' =>fake()->numberBetween(1, 10),
            'expiration' =>fake()->dateTimeBetween('now', '+1 year'),
            'salary_minimum' =>fake()->numberBetween(300, 1000),
            'salary_maximum' =>fake()->numberBetween(1001, 5000),
            'time_type' =>fake()->randomElement(['Full-time', 'Part-time']),
            'job_level' =>fake()->randomElement(['Entry', 'Mid', 'Senior']),
            'job_type' =>fake()->randomElement(['Permanent', 'Temporary', 'Internship']),
            'job_role' =>fake()->jobTitle(),
            'city' =>fake()->city(),
            'street' =>fake()->streetAddress(),
            'tags' =>fake()->words(3, true),
            'location' =>fake()->address(),
        ];
    }
}
