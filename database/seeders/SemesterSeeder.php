<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school_years = SchoolYear::all();
        foreach ($school_years as $schoolYear) {
            Semester::factory()->create([
                "school_year_id" => $schoolYear->id,
            ]);
        }
    }
}
