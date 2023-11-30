<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = \App\Models\Course::all();

        foreach ($courses as $course) {
            for ($i = 0; $i < 5; $i++) {
                $task = \App\Models\Task::factory()->create([
                    "course_id" => $course->id
                ]);
                $task->save();
            }
        }
    }
}
