<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_date = fake()->dateTimeBetween('2023-01-04', '2023-05-21')->format('Y-m-d');
        return [
            'name' => fake()->word(),
            'teacher' => fake()->name(),
            'start_date' => $start_date,
            'end_date' => fake()->dateTimeBetween($start_date,$start_date.'+60 days')->format('Y-m-d')
        ];
    }
}
