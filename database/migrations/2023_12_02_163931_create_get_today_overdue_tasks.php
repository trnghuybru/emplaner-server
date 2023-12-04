<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGetTodayOverdueTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = <<<'PROCEDURE'
        CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTodayOverdueTasks`(
            IN userId INT,
            IN currentDate DATE
          )
          BEGIN
            SELECT
              tasks.id,
              tasks.name,
              tasks.start_date,
              tasks.end_date
            FROM
              tasks
            INNER JOIN
              courses ON tasks.course_id = courses.id
            INNER JOIN
              semesters ON courses.semester_id = semesters.id
            INNER JOIN
              school_years ON semesters.school_year_id = school_years.id
            INNER JOIN
              users ON school_years.user_id = users.id
            WHERE
              users.id = userId AND
              tasks.end_date < currentDate AND
              DATEDIFF(currentDate, tasks.end_date) <= 7  -- tasks less than 1 week overdue
            ORDER BY
              tasks.start_date ASC;
          END
        PROCEDURE;

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the procedure when rolling back the migration
        DB::unprepared('DROP PROCEDURE IF EXISTS `GetTodayOverdueTasks`');
    }
}
