<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    //can tra ve tat ca cac task due vao dung ngay bat dau cua class
    public function get_detail_class(string $id){
        $class = SchoolClass::findOrFail($id);
        
        $class_date = DB::table('ClassSchedule')->where('class_id','=',$id)->value('class_day');
        dd($class_date);
        $tasks = $class->course->tasks->where(function ($task) use($class_date){
            return $task->end_date === $class_date;
        });
        $detailClass = [
            'id' => $class->id,
            'course_name' => $class->course->name,
            'start_time' => $class->schedules->start_time,
            'end_time' => $class->schedules->end_time,
            'room' => $class->room,
            'teacher' => $class->course->teacher,
            'tasks' => $tasks
        ]; 
        
        return response()->json([
            'status' => 200,
            'data' => $detailClass
        ]);
}
}