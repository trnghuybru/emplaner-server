<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_time = now()->setTime(random_int(7, 16), random_int(0, 59));
        
        return [
            "day_of_week" => fake()->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            'start_time' => $start_time->format('H:i'),
            'end_time' => now()->setTime($start_time->format('H'), $start_time->format('i'))->addMinutes(45)->format('H:i')
        ];
    }
}
