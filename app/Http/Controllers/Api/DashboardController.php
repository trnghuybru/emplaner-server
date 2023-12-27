<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Http\Resources\SchoolClassResource;
use App\Models\Exam;
use App\Models\Pomodoro;
use App\Models\SchoolClass;
use App\Models\Task;
use App\Models\User;
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
        $user = User::find(auth()->id());

        if ($user) {
            $today = now()->toDateString();

            $incompletedTaskCount = Task::whereHas('course.semester.school_year', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('end_date', '=', $today)
                ->where('status', '=', 0)
                ->count();

            $completedTaskCount = Task::whereHas('course.semester.school_year', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('end_date', '=', $today)
                ->where('status', '=', 1)
                ->count();

            $cumulativeTime = Pomodoro::where("user_id", "=", $user->id)
                ->where("date", $today)
                ->sum("time");

            $cumulativeTime = gmdate("H:i", $cumulativeTime * 60);

            return response()->json([
                "status" => 200,
                "data" => [
                    "incompleted_task" => $incompletedTaskCount,
                    "completed_task" => $completedTaskCount,
                    "cumulative_time" => $cumulativeTime
                ]
            ]);
        } else {
            return response()->json([
                "status" => 404,
                "message" => "User not found"
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function get_classes_exams(Request $request)
    {
        $user_id = $request->user()->id;
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $user = User::findOrFail($user_id);

        $classes = $user->school_years->flatMap(function ($schoolYear) {
            return $schoolYear->semesters->flatMap(function ($semester) {
                return $semester->courses->flatMap(function ($course) {
                    return $course->school_classes;
                });
            });
        });

        $classes->each(function ($class) {
            $class->course_name = $class->course->name;
            $class->teacher = $class->course->teacher;
            unset(
                $class->created_at,
                $class->updated_at,
                $class->end_time
            );
            unset($class->course);
            return $class->toArray();
        });

        $classes_today = $classes->where('date', '=', $today);
        $classes_tomorrow = $classes->where('date', '=', $tomorrow);
        //..................................................
        $exams = $user->school_years->flatMap(function ($schoolYear) {
            return $schoolYear->semesters->flatMap(function ($semester) {
                return $semester->courses->flatMap(function ($course) {
                    return $course->exams;
                });
            });
        });

        $exams->each(function ($ex) {
            $ex->course_name = $ex->course->name;
            $ex->teacher = $ex->course->teacher;
            unset($ex->created_at, $ex->updated_at);
            unset($ex->course);
            return $ex->toArray();
        });

        $exams_today = $exams->where('start_date', '=', $today);
        $exams_tomorrow = $exams->where('start_date', '=', $tomorrow);
        //----------------------------------


        return response()->json([
            "status" => 200,
            "data" => [
                "today" => [
                    "class" => $classes_today->values()->all(),
                    "exam" => $exams_today->values()->all()
                ],
                "tomorrow" => [
                    "class" => $classes_tomorrow->values()->all(),
                    "exam" => $exams_tomorrow->values()->all()
                ]
            ]
        ], 200);
    }


    public function get_due_tasks(Request $request)
    {

        $id = $request->user()->id;
        $today = now()->toDateString();

        $due_tasks = DB::select('CALL GetTodayDueTasks(?,?)', [
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

        $overdue_tasks = DB::select('CALL GetTodayOverdueTasks(?,?)', [
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
