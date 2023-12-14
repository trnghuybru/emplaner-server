<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateExamsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $view = <<<'VIEW'
            CREATE VIEW exams_view AS
            SELECT
                exams.id,
                exams.name,
                exams.start_date,
                exams.start_time,
                exams.duration,
                exams.room,
                exams.course_id,
                courses.name AS course_name,
                courses.color_code,
                semesters.id AS semesters_id,
                semesters.name AS semester_name,
                semesters.start_date AS semester_start_date,
                semesters.end_date AS semester_end_date,
                school_years.id AS school_years_id,
                school_years.start_date AS school_years_start_date,
                school_years.end_date AS school_years_end_date,
                users.id AS user_id
            FROM
                exams
            JOIN
                courses ON exams.course_id = courses.id
            JOIN
                semesters ON courses.semester_id = semesters.id
            JOIN
                school_years ON semesters.school_year_id = school_years.id
            JOIN
                users ON school_years.user_id = users.id;
        VIEW;

        DB::statement($view);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS exams_view');
    }
}
