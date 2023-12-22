<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskViewResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Task;
use App\Models\TypeTask;
use App\Models\User;
use Egulias\EmailValidator\Parser\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;

class TaskController extends Controller
{
    use CanLoadRelationships;
    /**
     * Display a listing of the resource.
     */

    private $relations = ['type_task'];
    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }


    public function index(Request $request)
    {
        $id = $request->user()->id;
        $task = $request->input('task_name');

        $tasks = DB::table('tasks_view')
            ->when($task, function ($query) use ($task) {
                $query->where('name', 'LIKE', '%' . $task . '%');
            })
            ->where('user_id', '=', $id)
            ->get();

        return response()->json([
            'status' => 200,
            'data' => TaskViewResource::collection($tasks),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'course_id' => 'integer|required',
            'name'  =>  'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'exam_id' => 'nullable',
            'type' => 'required|string'
        ]);

        $courseId = $request->input('course_id');

        $userId = DB::table('tasks_view')
        ->select('user_id')
        ->where('course_id', '=', $courseId)
        ->first()
        ->user_id;
        
        if ($request->exam_id !== null) {
            $exam = Exam::find($request->exam_id);
        
            if (!$exam || $exam->course->semester->school_year->user_id === auth()->id() && $courseId != $exam->course_id) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }
        
        if ($userId === auth()->id()) {
            $task = Task::create([
                'course_id' => $courseId,
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            $type_task = TypeTask::create([
                ...$request->validate([
                    'type' => 'required|string',
                    'exam_id' => 'integer|nullable'
                ]),
                'task_id' => $task->id
            ]);


            return response()->json([
                'status' => 201,
                'data' => new TaskResource($task)
            ]);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $userId = DB::table('tasks_view')
            ->select('user_id')
            ->where('id', '=', $task->id)
            ->first()
            ->user_id;

        if ($userId === auth()->id()) {
            $taskDetail = DB::table('tasks_view')->select([
                'id',
                'name',
                'description',
                'end_date',
                'type',
                'exam_id',
                'course_id',
                'course_name'
            ])->where('id', '=', $task->id)
                ->first();
            if ($taskDetail->exam_id != null) {
                $exam = Exam::find($taskDetail->exam_id);

                $taskDetail->exam_id = $exam->id;

                $taskDetail->exam_name = $exam->name;
            }

            return response()->json([
                'status' => 200,
                'data' => $taskDetail
            ]);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'course_id' => 'integer|required',
            'name'  =>  'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required',
            'exam_id' => 'nullable',
            'type' => 'required|string'
        ]);

        $courseId = $request->input('course_id');

        $task = Task::find($id);

        if ($courseId == $task->course_id && $task->course->semester->school_year->user->id == auth()->id()) {
            $task->update([
                'course_id' => $courseId,
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status
            ]);
            

            $typeTask = $task->type_task;
            $typeTask->update([
                'type' => $request->type,
                'exam_id' => $request->exam_id
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Updated Successfully"
            ]);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $userId = DB::table('tasks_view')
            ->select('user_id')
            ->where('id', '=', $task->id)
            ->first()
            ->user_id;

        if ($userId === auth()->id()) {
            TypeTask::where('task_id', $task->id)->delete();
            $task->delete();

            return response()->json([
                'status' => 200,
                'message' => "Delete successfully"
            ], 200);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }

    public function get_exams_by_course(string $id){
        $course = Course::find($id);

        if ($course && $course->semester->school_year->user_id == auth()->id()){
            $today = now()->toDateString();
            $exams = $course->exams->where('course_id','=',$id)->where('start_date','>=',$today);
        }

        $exams->each(function ($e){
            unset($e->start_time);
            unset($e->start_date);
            unset($e->duration);
            unset($e->room);
            unset($e->created_at);
            unset($e->updated_at);
        });
        return response()->json([
            'status' => 200,
            'data' => $exams
        ]);
    }

    
}
