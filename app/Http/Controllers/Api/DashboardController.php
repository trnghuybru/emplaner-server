<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Http\Resources\SchoolClassResource;
use App\Models\Exam;
use App\Models\Pomodoro;
use App\Models\SchoolClass;
use App\Models\Task;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }

    //Incompleted - Completed Tash & Cumulative Time
    public function get_today_detail(Request $request)
    {
        $id = $request->user()->id;
        $today = date("Y-m-d");
        //Incomplete Task
        // $noTaskIncomplete  = Task::join('courses', 'tasks.course_id', '=', 'courses.id')
        // ->join('semesters', 'courses.semester_id', '=', 'semesters.id')
        // ->join('school_years', 'semesters.school_year_id', '=', 'school_years.id')
        // ->join('users', 'school_years.user_id', '=', 'users.id')
        // ->where('tasks.end_date', $today)
        // ->where('tasks.status', 0)
        // ->where('users.id', $id)
        $noTaskIncomplete = DB::table('tasks_view')
        ->where('end_date', '=', $today) 
        ->where('status', '=', 0)
        ->where('user_id', '=', $id)
        ->count();
        //Completed Task
        // $noTaskComplete = Task::join('courses', 'tasks.course_id', '=', 'courses.id')
        // ->join('semesters', 'courses.semester_id', '=', 'semesters.id')
        // ->join('school_years', 'semesters.school_year_id', '=', 'school_years.id')
        // ->join('users', 'school_years.user_id', '=', 'users.id')
        // ->where('tasks.end_date', $today)
        // ->where('tasks.status', 1) 
        // ->where('users.id', $id)
        // ->count();
        $noTaskComplete = DB::table('tasks_view')
        ->where('end_date', '=', $today)
        ->where('status', '=', 1)
        ->where('user_id', '=', $id) 
        ->count();
        //Cumulative Time
        $minute = Pomodoro::where("user_id","=", $id)
        ->where("date", $today)->sum("time");
        $cumulativeTime = new DateTime();
        $cumulativeTime->setTime(0, $minute, 0);

        return response()->json([
            "status" => 200,
            "data" => [
                "incompleted_task" => $noTaskIncomplete,
                "completed_task" => $noTaskComplete,
                "cumulative_time" => $cumulativeTime->format("H:i")
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function get_classes_exams(Request $request)
    {
        $id = $request->user()->id;
        $today = now()->toDateString();

        $currentDayOfWeek = now()->format('l');

        $tomorrow = Carbon::tomorrow()->toDateString();
        $tomorrowDay = Carbon::tomorrow()->format('l');

        // $classes_schedule = SchoolClass::join('schedules', 'school_classes.id', '=', 'schedules.class_id')
        //     ->join('courses', 'school_classes.course_id', '=', 'courses.id')
        //     ->join('semesters', 'courses.semester_id', '=', 'semesters.id')
        //     ->join('school_years', 'semesters.school_year_id', '=', 'school_years.id')
        //     ->join('users', 'school_years.user_id', '=', 'users.id')
        //     ->where('schedules.day_of_week', '=', $currentDayOfWeek)
        //     ->where('school_classes.start_date', '<=', $today)
        //     ->where(function ($query) use ($today) {
        //         $query->where('school_classes.end_date', '>=', $today)
        //             ->orWhereNull('school_classes.end_date');
        //     })
        //     ->whereRaw("date_add(school_classes.start_date, interval dayofweek(school_classes.start_date) - 1 day) BETWEEN school_classes.start_date AND school_classes.end_date")
        //     ->where('users.id', '=', $id) 
        //     ->select('school_classes.id as class_id', 'school_classes.teacher', 'school_classes.start_date', 'school_classes.end_date', 'schedules.day_of_week', 'schedules.start_time', 'schedules.end_time', 'courses.name as course_name')
        //     ->get();

        $classes_schedule = DB::select('CALL GetClassesSchedule(?, ?, ?)', [
            $today,
            $currentDayOfWeek,
            $id
        ]);

        // $exams = Exam::join('courses', 'exams.course_id', '=', 'courses.id')
        //     ->join('semesters', 'courses.semester_id', '=', 'semesters.id')
        //     ->join('school_years', 'semesters.school_year_id', '=', 'school_years.id')
        //     ->join('users', 'school_years.user_id', '=', 'users.id')
        //     ->where('exams.start_date', '=', $today)
        //     ->select('exams.id', 'exams.name', 'exams.start_date', 'exams.start_time', 'exams.duration', 'exams.room', 'courses.name as course_name')
        //     ->get();

        $exams = DB::select('CALL GetTodayExams(?,?)', [
            $id,
            $today
        ]);
        
        $classes_schedule_tomorrow = DB::select('CALL GetClassesSchedule(?, ?, ?)', [
            $tomorrow,
            $tomorrowDay,
            $id
        ]);
        
        $exams_tomorrow = DB::select('CALL GetTodayExams(?,?)', [
            $id,
            $tomorrow
        ]);

        return response()->json(
            [
                "status" => 200,
                "data" => [
                    "today" => [
                        "classes" => [
                            "type" => "class",
                            "data" => SchoolClassResource::collection($classes_schedule)
                        ],
                        "exams" => [
                            "type" => "exam",
                            "data" => ExamResource::collection($exams)
                        ]
                    ],
                    "tomorrow" => [
                        "classes" => [
                            "type" => "class",
                            "data" => SchoolClassResource::collection($classes_schedule_tomorrow)
                        ],
                        "exams" => [
                            "type" => "exam",
                            "data" => ExamResource::collection($exams_tomorrow)
                        ]
                    ]
                ]
            ]

        );
    }

    public function get_due_tasks(Request $request){
        $id = $request->user()->id;
        $today = now()->toDateString();
        
        $due_tasks = DB::select('CALL GetTodayDueTasks(?,?)',[
            $id,
            $today
        ]);

        return response()->json([
            "status" => 200,
            "data" => $due_tasks
        ]);
    }

    public function get_overdue_tasks(Request $request)
    {
        $id = $request->user()->id;
        $today = now()->toDateString();
        
        $overdue_tasks = DB::select('CALL GetTodayOverdueTasks(?,?)',[
            $id,
            $today
        ]);

        return response()->json([
            "status" => 200,
            "data" => $overdue_tasks
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
