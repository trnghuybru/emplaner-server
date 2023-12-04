<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{

    public function index(Request $request)
    {
        $id = $request->user()->id;
        $exams = Exam::where('user_id', $id)->get();

        return response()->json([
            'status' => 200,
            'data' => ExamResource::collection($exams),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'integer|required|exists:courses,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required|string',
            'duration' => 'required|integer',
            'room' => 'required|string',
        ]);

        $exam = Exam::create([
            'course_id' => $request->course_id,
            'name' => $request->name,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'room' => $request->room,
        ]);

        return response()->json([
            'status' => 201,
            'data' => new ExamResource($exam),
        ]);
    }

    public function show($id)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return response()->json(['message' => 'Exam not found'], 404);
        }

        // Kiểm tra xem người dùng có quyền xem exam không
        if ($exam->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => 200,
            'data' => new ExamResource($exam),
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'integer|exists:courses,id',
            'name' => 'string|max:255',
            'start_date' => 'date',
            'start_time' => 'string',
            'duration' => 'integer',
            'room' => 'string',
        ]);

        $exam = Exam::findOrFail($id);

        // Kiểm tra xem người dùng có quyền sửa exam không
        if ($exam->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $exam->update($request->all());

        return response()->json([
            'status' => 200,
            'data' => new ExamResource($exam),
        ]);
    }


    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);

        // Kiểm tra xem người dùng có quyền xóa exam không
        if ($exam->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $exam->delete();

        return response()->json([
            'status' => 204,
        ]);
    }
}
