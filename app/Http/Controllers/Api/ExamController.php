<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Exam;
use App\Models\Task;
use App\Models\TypeTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Matcher\Type;

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

        $user = User::find(auth()->id());
    
        if ($exam->course->semester->school_year->user_id == $user->id) {
            $examDetail = $exam->load(['course.semester.school_year']);

            $typeTasks = TypeTask::where('exam_id','=',$exam->id)->get();
            
            $tasks = $typeTasks->map(function ($typeTask) {
                $task = Task::find($typeTask->task_id);
                if ($task) {
                    return [
                        'id' => $task->id,
                        'course_id' => $task->course_id,
                        'name' => $task->name,
                        'type' => $task->type_task->type,
                        'end_date' => $task->end_date,
                        'course_name' => $task->course->name,
                        'color_code' => $task->course->color_code,
                        'status' => $task->status
                    ];
                }
                return null; 
            })->filter();
            
            return response()->json([
                'status' => 200,
                'data' => [
                    'exam' => $examDetail,
                    'tasks' => $tasks
                ]
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

        $user = User::find(auth()->id());

        if ($user) {
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
                'data' => $exam
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
            'course_id' => 'integer|required',
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
                'course_id' => $request->course_id,
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

            if ($exam->type_task !== null && $exam->type_task->exam_id !== null) {
                $exam->type_task->update(['exam_id' => null]);
            }
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
