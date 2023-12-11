<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    use CanLoadRelationships;

    private $relations = []; // Thêm các quan hệ nếu cần

    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $examName = $request->input('exam_name');

        $exams = DB::table('exams_view')
            ->when($examName, function ($query) use ($examName) {
                $query->where('name', 'LIKE', '%' . $examName . '%');
            })
            ->where('user_id', '=', $userId)
            ->get();

        return response()->json([
            'status' => 200,
            'data' => ExamResource::collection($exams),
        ]);
    }




    public function show(Exam $exam)
{
    if (!$exam) {
        return response()->json([
            'status' => 404,
            'message' => 'Exam not found'
        ], 404);
    }

    $userId = DB::table('exams_view')
        ->select('user_id')
        ->where('id', '=', $exam->id)
        ->first()
        ->user_id;

    if ($userId === auth()->id()) {
        $examDetail = DB::table('exams_view')->select([
            'id',
            'name',
            'start_date',
            'start_time',
            'duration',
            'room',
            'course_id',
            'course_name',
            'color_code',
            'semesters_id',
            'semester_name',
            'semester_start_date',
            'semester_end_date',
            'school_years_id',
            'school_years_start_date',
            'school_years_end_date',
            'user_id'
        ])->where('id', '=', $exam->id)
            ->first();

        return response()->json([
            'status' => 200,
            'data' => $examDetail
        ]);
    } else {
        return response()->json([
            'status' => 403,
            'message' => 'Unauthorized'
        ], 403);
    }
}


    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'integer|required',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'duration' => 'required|integer',
            'room' => 'required|string|max:255',
        ]);

        $courseId = $request->input('course_id');

        $userId = DB::table('exams_view')
            ->select('user_id')
            ->where('course_id', '=', $courseId)
            ->first()
            ->user_id;

        if ($userId === auth()->id()) {
            $exam = Exam::create([
                'course_id' => $courseId,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'room' => $request->room,
            ]);

            return response()->json([
                'status' => 201,
                'data' => new ExamResource($exam)
            ]);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'name' => 'string|max:255',
            'start_date' => 'date',
            'start_time' => 'date_format:H:i',
            'duration' => 'integer',
            'room' => 'string|max:255',
        ]);

        $userId = DB::table('exams_view')
            ->select('user_id')
            ->where('id', '=', $exam->id)
            ->first()
            ->user_id;

        if ($userId === auth()->id()) {
            $exam->update([
                'name' => $request->input('name', $exam->name),
                'start_date' => $request->input('start_date', $exam->start_date),
                'start_time' => $request->input('start_time', $exam->start_time),
                'duration' => $request->input('duration', $exam->duration),
                'room' => $request->input('room', $exam->room),
            ]);

            return response()->json([
                'status' => 200,
                'data' => new ExamResource($exam)
            ]);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }

    public function destroy(Exam $exam)
    {
        $userId = DB::table('exams_view')
            ->select('user_id')
            ->where('id', '=', $exam->id)
            ->first()
            ->user_id;

        if ($userId === auth()->id()) {
            $exam->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Delete successfully'
            ]);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }
}
