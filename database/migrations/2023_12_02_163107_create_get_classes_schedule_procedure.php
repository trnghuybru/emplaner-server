<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGetClassesScheduleProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = <<<'PROCEDURE'
            CREATE PROCEDURE `GetClassesSchedule`(
                IN currentDate DATE,
                IN currentDayOfWeek VARCHAR(10),
                IN userId INT
            )
            BEGIN
                SELECT
                    school_classes.id AS class_id,
                    courses.teacher,
                    courses.start_date,
                    courses.end_date,
                    schedules.day_of_week,
                    schedules.start_time,
                    schedules.end_time,
                    school_classes.room,
                    courses.name AS course_name
                FROM
                    school_classes
                INNER JOIN
                    schedules ON school_classes.id = schedules.class_id
                INNER JOIN
                    courses ON school_classes.course_id = courses.id
                INNER JOIN
                    semesters ON courses.semester_id = semesters.id
                INNER JOIN
                    school_years ON semesters.school_year_id = school_years.id
                INNER JOIN
                    users ON school_years.user_id = users.id
                WHERE
                    CAST(schedules.day_of_week AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci = currentDayOfWeek COLLATE utf8mb4_unicode_ci
                    AND courses.start_date <= currentDate
                    AND (courses.end_date >= currentDate OR courses.end_date IS NULL)
                    AND DATE_ADD(courses.start_date, INTERVAL DAYOFWEEK(courses.start_date) - 1 DAY) BETWEEN courses.start_date AND courses.end_date
                    AND users.id = userId;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS `GetClassesSchedule`');
    }
}
