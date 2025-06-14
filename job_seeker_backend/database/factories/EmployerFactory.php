<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employer>
 */
class EmployerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1,10),
            'company_name'=> fake()->company(),
            'company_address' => fake()->address(),
            'company_phone' => fake()->phoneNumber(),
            'company_email' => fake()->email(),
            'company_description' => fake()->text(200), // تحديد الحد الأقصى 200 حرف
            'company_logo' => fake()->imageUrl(),
            'company_cover' => fake()->imageUrl(),
            'company_social' => fake()->url()
        ];
    }
}
