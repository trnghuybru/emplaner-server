<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTasksView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $view = <<<'VIEW'
            CREATE VIEW tasks_view AS
            SELECT
                tasks.id,
                tasks.name,
                tasks.description,
                tasks.end_date,
                tasks.status,
                type_tasks.exam_id,
                type_tasks.type,
                tasks.course_id,
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
                tasks
            JOIN
                courses ON tasks.course_id = courses.id
            JOIN
                semesters ON courses.semester_id = semesters.id
            JOIN
                school_years ON semesters.school_year_id = school_years.id
            JOIN
                users ON school_years.user_id = users.id
            JOIN
                type_tasks ON tasks.id = type_tasks.task_id;
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
        DB::statement('DROP VIEW IF EXISTS tasks_view');
    }
}
