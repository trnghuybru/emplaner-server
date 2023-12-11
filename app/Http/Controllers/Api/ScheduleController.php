<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassViewResource;
use App\Http\Resources\SchoolClassResource;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    
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
            'teacher' => 'string|required',
            'start_time' => 'date_format:H:i|required',
            'end_time' => 'date_format:H:i|required',
            'day_of_week' => 'string|nullable'
        ]);


        $class = SchoolClass::create([
            'course_id' => $request->course_id,
            'room' => $request->room
        ]);
        $dayOfWeekArray = explode(',', $request->day_of_week);
        $dayOfWeekArray = array_map('trim', $dayOfWeekArray);

        for ($i = 0; $i < count($dayOfWeekArray); $i++) {
            Schedule::create([
                'class_id' => $class->id,
                'day_of_week' => $dayOfWeekArray[$i],
                'start_time' => $request->start_time,
                'end_time' => $request->end_time
            ]);
        }

        return response()->json([
            'status' => 201,
            'message' => 'Created successfully'
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

    public function destroy_course(string $id)
    {
        $course = Course::find($id);

        $tasks = $course->tasks;
        foreach ($tasks as $task){
            if ($task->type_task) {
                $task->type_task->delete();
            }
            $task->delete();
        }

        $exams = $course->exams;
        foreach($exams as $exam){
            if ($exam->type_task){
                $exam->type_task->delete();
            }
            $exam->delete();
        }

        $classes = $course->school_classes;
        foreach($classes as $class){
            if ($class->schedules){
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
