<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Course;
class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        return response()->json($exams, 200);
    }

    public function show($id)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return response()->json(['message' => 'Exam not found'], 404);
        }

        return response()->json($exam, 200);
    }

    public function store(Request $request)
{
    $request->validate([
        'course_id' => 'required|integer|exists:courses,id',
        'name' => 'required|string',
        'start_date' => 'required|date',
        'start_time' => 'required|string',
        'duration' => 'required|integer',
        'room' => 'required|string',
    ]);

    $exam = new Exam();
    $exam->course_id = $request->input('course_id');
    $exam->name = $request->input('name');
    $exam->start_date = $request->input('start_date');
    $exam->start_time = $request->input('start_time');
    $exam->duration = $request->input('duration');
    $exam->room = $request->input('room');

    $exam->save();

    return response()->json($exam, 201);
}


public function update(Request $request, $id)
{
    $request->validate([
        'course_id' => 'required|integer',
        'name' => 'required|string',
        'start_date' => 'required|date',
        'start_time' => 'required|string',
        'duration' => 'required|integer',
        'room' => 'required|string',
    ]);

    $exam = Exam::findOrFail($id);
    $exam->update($request->all());

    return response()->json($exam, 200);
}


public function destroy($id)
{
    $exam = Exam::findOrFail($id);
    $exam->delete();

    return response()->json(null, 204);
}
}



