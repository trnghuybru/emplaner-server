<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function __construct(){
        $this->middleware("auth:sanctum");
    }
    //can tra ve tat ca cac task due vao dung ngay bat dau cua class
    public function get_detail_class(string $id){
        $class = SchoolClass::findOrFail($id);

        $tasks = $class->course->tasks->where('end_date','=',$class->date);
        $tasks->each(function($t){
            unset($t->created_at,$t->updated_at,$t->description,$t->start_date,$t->end_date);
            $t->type = $t->type_task->type;
            unset($t->type_task);
        });

        if(!$class){
            return response()->json([
                'status' => 400,
                'message' => "Not found"
            ],400);
        }

        if($class->course->semester->school_year->user_id === auth()->id()){
            unset($class->course);
            unset($class->created_at,$class->updated_at);
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
        ],400);
        
}
}
