<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(random_int(2,4)),
            'start_date' => fake()->dateTimeBetween('2023-12-23','2024-12-12')->format('Y-m-d'),
            'start_time' => now()->setTime(random_int(7, 16), random_int(0, 59))->format('H:i'),
            'duration' => random_int(15,150),
            'room' => strval(random_int(1, 200))
        ];
    }
}
