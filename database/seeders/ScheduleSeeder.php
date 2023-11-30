<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolclasses = \App\Models\SchoolClass::all();
        foreach ($schoolclasses as $schoolclass) {
            \App\Models\Schedule::factory()->create([
                "class_id"=> $schoolclass->id,
            ]);
        }
    }
}
