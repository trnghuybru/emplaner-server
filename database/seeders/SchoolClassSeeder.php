<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = \App\Models\Course::all();
        foreach ($courses as $course) {
            for ($i = 0; $i < 10; $i++) {
                \App\Models\SchoolClass::factory()->create([
                    "course_id"=> $course->id,
                ]);
            }
        }
    }
}
