<?php

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition()
    {
        $types = ['Monthly', 'Quarterly', 'Annual', 'Custom'];

        return [
            'title' => fake()->sentence(),
            'type' => $this->faker->randomElement($types),
            'description' => $this->faker->paragraph(),
            'last_updated' => $this->faker->dateTimeThisMonth(),
            'views' => $this->faker->numberBetween(0, 1000),
            'downloads' => $this->faker->numberBetween(0, int2: 500),
            'icon' => 'fa-' . $this->faker->randomElement(['chart-bar', 'chart-line', 'users', 'file-alt']),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => $this->faker->dateTimeThisMonth()
        ];
    }
}
