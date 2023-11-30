<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(5)->create();
        $this->call(SchoolYearSeeder::class);
        $this->call(SemesterSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(ExamSeeder::class);
        $this->call(TaskSeeder::class);
        $this->call(TypeTaskSeeder::class);
        $this->call(SchoolClassSeeder::class);
        $this->call(ScheduleSeeder::class);
        $this->call(PomodoroSeeder::class);
    }
}
