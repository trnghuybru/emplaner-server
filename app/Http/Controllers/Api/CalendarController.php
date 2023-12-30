<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ProcessDataDateType;
use App\Models\SchoolClass;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }
    //can tra ve tat ca cac task due vao dung ngay bat dau cua class
    public function get_detail_class(string $id)
    {
        $class = SchoolClass::findOrFail($id);

        $tasks = $class->course->tasks->where('end_date', '=', $class->date);
        $tasks->each(function ($t) {
            $t->course_name = $t->course->name;
            unset($t->created_at, $t->updated_at, $t->description);
            $t->type = $t->type_task->type;
            unset($t->type_task);
        });

        if (!$class) {
            return response()->json([
                'status' => 400,
                'message' => "Not found"
            ], 400);
        }

        if ($class->course->semester->school_year->user_id === auth()->id()) {
            $class->course_name = $class->course->name;
            $class->teacher = $class->course->teacher;
            unset($class->course);
            unset($class->created_at, $class->updated_at);
            return response()->json([
                'status' => 200,
                'data' => [
                    'class' => $class,
                    'tasks' => $tasks
                ]
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => "Error"
        ], 400);
    }

    public function destroy(string $id)
    {
        $class = SchoolClass::find($id);

        if (!$class) {
            return response()->json([
                "status" => 400,
                "message" => "Not found"
            ], 400);
        }

        if (auth()->id() === $class->course->semester->school_year->user_id) {
            if ($class->delete()) {
                return response()->json([
                    "status" => 200,
                    "message" => "Deleted successfully"
                ], 200);
            } else {
                return response()->json(['status' => 500], 500);
            }
        }
        return response()->json([
            'status' => 403,
            'message' => 'Unauthorized'
        ], 403);
    }

    public function update(Request $request, string $id)
    {

        $request->validate([
            'course_id' => 'required|integer',
            'room' => 'string|required',
            'date' => 'date',
            'start_time' => 'date_format:H:i|required',
            'end_time' => 'date_format:H:i|required'
        ]);


        $class = SchoolClass::find($id);

        if (!$class) {
            return response()->json([
                "status" => 400,
                "message" => "Not found"
            ], 400);
        }

        if (auth()->id() === $class->course->semester->school_year->user_id) {
            $class->update([
                'course_id' => $request->course_id,
                'room' => $request->room,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'day_of_week' => Carbon::parse($request->date)->format('l')
            ]);
            return response()->json([
                'status' => 200,
                'data' => "Updated Successfully"
            ]);
        }
        return response()->json([
            'status' => 403,
            'message' => 'Unauthorized'
        ], 403);
    }

    public function get_list_classes()
    {
        $userId = auth()->id();
        if ($userId) {
            $user = User::find($userId);
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
                unset($class->course);
            });
            return response()->json([
                'status' => 200,
                'data' => $classes
            ], 200);
        }
    }

    public function delete_tasks(Request $request)
    {
        $request->validate([
            'arrId' => 'array|required'
        ]);
        $arrId = $request->arrId;

        for ($i = 0; $i < count($arrId); $i++) {
            $task = Task::find($arrId[$i]);
            $task->type_task->delete();
            $task->delete();
        }
        return response()->json([
            'status' => 200,
            'message' => "Delete successfully"
        ]);
    }
}
