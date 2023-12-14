<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $startTime = now()->setTime(random_int(7, 16), random_int(0, 59));
        $endTime = $startTime->copy()->addHours(random_int(1, 4))->addMinutes(random_int(0, 59));

        return [
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'room' => strval(random_int(1, 200)),
            'date' => fake()->date(),
        ];
    }
}
