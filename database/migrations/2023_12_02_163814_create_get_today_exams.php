<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGetTodayExams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = <<<'PROCEDURE'
        CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTodayExams`(
            IN userId INT,
            IN currentDate DATE
          )
          BEGIN
            SELECT
              exams.id,
              exams.name,
              exams.start_date,
              exams.start_time,
              exams.duration,
              exams.room,
              courses.name AS course_name
            FROM
              exams
            INNER JOIN
              courses ON exams.course_id = courses.id
            INNER JOIN
              semesters ON courses.semester_id = semesters.id
            INNER JOIN
              school_years ON semesters.school_year_id = school_years.id
            INNER JOIN
              users ON school_years.user_id = users.id
            WHERE
              users.id = userId AND
              exams.start_date = currentDate;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS `GetTodayExams`');
    }
}
