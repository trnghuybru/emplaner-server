<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassViewResource;
use App\Http\Resources\SchoolClassResource;
use App\Models\SchoolYear;
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
        
        if($userId === SchoolYear::where('id', $schoolYearId)->value('user_id')){
            
            if($semesterId == null){
                $classes = DB::table('classes_view')
                        ->where('school_years_id','=',$schoolYearId)
                        ->distinct()
                        ->select('course_id','course_name','teacher','semester_start_date','semester_end_date')
                        ->get();
            } else{
                $classes = DB::table('classes_view')
                        ->where('school_years_id','=',$schoolYearId)
                        ->where('semester_id','=',$semesterId)
                        ->select('course_id','course_name','teacher','color_code','semester_start_date','semester_end_date')
                        ->get();
            }

            return response()->json([
                'status' => 200,
                'data' => $classes
            ],200);
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ],401);
        }

        
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
