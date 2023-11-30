<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Task;
use App\Models\TypeTask;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();

        foreach ($tasks as $task) {
            TypeTask::factory()->create([
                "task_id" => $task->id,
            ]);
        }

        $task_types = TypeTask::all();

        foreach ($task_types as $task_type) {
            if ($task_type->type === "Revision") {
                $exam = Exam::inRandomOrder()->first();
                $task_type->exam_id = $exam->id;
                $task_type->save();
            }
        }
    }
}
