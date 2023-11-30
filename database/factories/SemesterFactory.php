<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester>
 */
class SemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'start_date' => fake()->dateTimeBetween('2023-01-01','2023-05-31')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('2023-06-01','2023-12-31')->format('Y-m-d')
        ];
    }
}
