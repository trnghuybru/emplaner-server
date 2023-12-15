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
        $user_id = $request->user()->id;
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $examsToday = $this->getExams($user_id, $today);
        $examsTomorrow = $this->getExams($user_id, $tomorrow);

        $result = [
            "status" => 200,
            "data" => [
                "today" => $examsToday,
                "tomorrow" => $examsTomorrow
            ]
        ];

        return response()->json($result);
    }

    private function getExams($user_id, $date)
    {
        return DB::table('exams_view')
            ->where('user_id', $user_id)
            ->whereDate('start_date', $date)
            ->select('name', 'start_date', 'room')
            ->get();
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
