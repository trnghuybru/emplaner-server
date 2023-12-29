<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Traits\ProcessDataDateType;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    use ProcessDataDateType;
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }

    public function get_class_list(Request $request)
    {
        $userId = auth()->id();
        $schoolYearId = $request->query('school-year-id');
        $semesterId = $request->query('semester-id');

        if ($userId === SchoolYear::where('id', $schoolYearId)->value('user_id')) {

            if ($semesterId == null) {
                $classes = DB::table('classes_view')
                    ->where('school_years_id', '=', $schoolYearId)
                    ->distinct()
                    ->select('course_id', 'course_name', 'teacher', 'color_code', 'semester_start_date', 'semester_end_date')
                    ->get();
            } else {
                $classes = DB::table('classes_view')
                    ->where('semesters_id', '=', $semesterId)
                    ->distinct()
                    ->select('course_id', 'course_name', 'teacher', 'color_code', 'semester_start_date', 'semester_end_date')
                    ->get();
            }

            return response()->json([
                'status' => 200,
                'data' => $classes
            ], 200);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_class(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'room' => 'string|required',
            'start_time' => 'date_format:H:i|required',
            'end_time' => 'date_format:H:i|required',
            'day_of_week' => 'string|required'
        ]);

        $startDate = Course::findOrFail($request->course_id)->start_date;
        $endDate = Course::findOrFail($request->course_id)->end_date;
        $day_of_week = $request->day_of_week;

        $days = explode(",", $day_of_week);

        
        $resultArray = [];


        foreach ($days as $day) {
            $dateArray = $this->generateWeekdays($day, $startDate, $endDate);
            $resultArray = array_merge($resultArray, $dateArray);
        }



        for ($i = 0; $i < count($resultArray); $i++) {
            SchoolClass::create([
                'course_id' => $request->course_id,
                'room' => $request->room,
                'date' => $resultArray[$i],
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'day_of_week' => $request->day_of_week
            ]);
        }

        return response()->json([
            'status' => 201,
            'message' => 'Created successfully'
        ]);
    }

    public function get_course_detail(string $id)
    {
        $course = Course::findOrFail($id);
        return response()->json([
            "status" => 200,
            "data" => new CourseResource($course)
        ]);
    }

    public function store_course(Request $request)
    {
        $request->validate([
            'semester_id' => 'integer|required',
            'name' => 'string|required',
            'color_code' => 'required',
            'teacher' => 'string|required',
            'start_date' => 'date_format:Y-m-d|required',
            'end_date' => 'date_format:Y-m-d|required'
        ]);

        $course = Course::create([
            'semester_id' => $request->semester_id,
            'name' => $request->name,
            'color_code' => $request->color_code,
            'teacher' => $request->teacher,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        if ($course) {
            return response()->json([
                'status' => 201,
                'message' => 'Created successfully'
            ], 201);
        }
    }
    public function show(string $id)
    {
        $user = User::find(auth()->id());

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found'
            ], 404);
        }

        $course = Course::with(['semester', 'tasks', 'exams', 'school_classes'])->find($id);

        if (!$course) {
            return response()->json([
                'status' => 404,
                'message' => 'Course not found'
            ], 404);
        }

        $schoolYearUserId = optional($course->semester->school_year->user)->id;

        if ($schoolYearUserId !== $user->id) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }

        $courseDetail = [
            'id' => $course->id,
            'name' => $course->name,
            'color_code' => $course->color_code,
            'teacher' => $course->teacher,
            'start_date' => $course->start_date,
            'end_date' => $course->end_date,
            'semester' => [
                'id' => $course->semester->id,
                'name' => $course->semester->name,
            ],
            'tasks' => $course->tasks,
            'exams' => $course->exams,
            'school_classes' => $course->school_classes,
        ];

        return response()->json([
            'status' => 200,
            'data' => $courseDetail
        ]);
    }
    public function update_course(Request $request, string $id)
    {
        $request->validate([
            'semester_id' => 'integer|required',
            'name' => 'string|required',
            'teacher' => 'string|required',
            'color_code' => 'nullable|string',
            'start_date' => 'date_format:Y-m-d|required',
            'end_date' => 'date_format:Y-m-d|required'
        ]);

        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 404,
                'message' => 'Course not found'
            ], 404);
        }

        $semesterId = $request->input('semester_id');

        if ($semesterId == $course->semester_id && $course->semester->school_year->user->id == auth()->id()) {
            $course->update([
                'name' => $request->name,
                'teacher' => $request->teacher,
                'color_code' => $request->color_code,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Updated successfully',
            ]);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }

    public function destroy_course(string $id)
    {
        $course = Course::find($id);

        $tasks = $course->tasks;
        foreach ($tasks as $task) {
            if ($task->type_task) {
                $task->type_task->delete();
            }
            $task->delete();
        }

        $exams = $course->exams;
        foreach ($exams as $exam) {
            if ($exam->type_task) {
                $exam->type_task->delete();
            }
            $exam->delete();
        }

        $classes = $course->school_classes;
        foreach ($classes as $class) {
            if ($class->schedules) {
                $class->schedules()->delete();
            }
            $class->delete();
        }

        if ($course->delete()) {
            return response()->json([
                "status" => 200,
                "message" => "Deleted successfully"
            ]);
        }
    }

    public function store_semester(Request $request)
    {
        $semester = Semester::create([
            ...$request->validate([
                'school_year_id' => 'integer|required',
                'name' => 'string|required',
                'start_date' => 'date_format:Y-m-d|required',
                'end_date' => 'date_format:Y-m-d|required'
            ])
        ]);
        if ($semester) {
            return response()->json([
                'status' => 201,
                'message' => 'Created successfully'
            ], 201);
        }
    }

    
}
