<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskViewResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Task;
use App\Models\TypeTask;
use App\Models\User;
use Egulias\EmailValidator\Parser\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'exam_id' => 'nullable'
        ]);

        $courseId = $request->input('course_id');

        $userId = DB::table('tasks_view')
        ->select('user_id')
        ->where('course_id', '=', $courseId)
        ->first()
        ->user_id;
    

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
                    'exam_id' => 'integer'
                ]),
                'task_id' => $task->id
            ]);
            return response()->json([
                'status' => 201,
                'data' => new TaskResource($task)
            ]);
        }else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ],403);
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
        
    }
}
