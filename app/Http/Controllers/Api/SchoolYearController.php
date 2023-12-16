<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ExamResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
Use DB;
class SchoolYearController extends Controller
{
    use CanLoadRelationships;

    private $relations = []; // Thêm các quan hệ nếu cần

    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $userId = auth()->id();

        $schoolYear = SchoolYear::create([
            'user_id' => $userId,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => 201,
            'data' => $schoolYear,
        ]);
    }


    public function update(Request $request, $id)
    {
        $schoolYear = SchoolYear::find($id);

        if (!$schoolYear) {
            return response()->json([
                'status' => 404,
                'message' => 'School year not found',
            ], 404);
        }

        $userId = auth()->id();

        if ($schoolYear->user_id !== $userId) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $schoolYear->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => 200,
            'data' => $schoolYear,
        ]);
    }

}
