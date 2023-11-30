<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = \App\Models\Course::all();
        foreach ($courses as $course) {
            for ($i = 0; $i < 2; $i++) {
                \App\Models\Exam::factory()->create([
                    "course_id"=> $course->id,
                ]);
            }
        }
    }
}
