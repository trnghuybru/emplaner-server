<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        $start_date = fake()->dateTimeBetween('now', '2023-12-21')->format('Y-m-d');
        return [
            'name' => fake()->sentence(random_int(4,7)),
            'description' => fake()->paragraph(1),
            'start_date' => $start_date,
            'end_date' => fake()->dateTimeBetween($start_date,$start_date.'+10 days')->format('Y-m-d'),
            'status' => random_int(0,1)
        ];
    }
}
