<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = \App\Models\Semester::all();
        foreach ($semesters as $semester) {
            for ($i = 0; $i < 5; $i++) {
                \App\Models\Course::factory()->create([
                    'semester_id'=>$semester->id
                ]);
            }
        }
    }
}
