<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ExamResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\User;
Use App\Models\Semester;
Use DB;
class SchoolYearController extends Controller
{
    use CanLoadRelationships;

    private $relations = []; // Thêm các quan hệ nếu cần

    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }

    public function index(){
        $user = User::find(auth()->id());
        if($user){
            $schoolYears = $user->school_years;
            return response()->json([
                'status' => 200,
                'data' => $schoolYears
            ]);
        }
    }

    public function show(string $id){
        $user = User::find(auth()->id());

        if ($user){
            $schoolYear = SchoolYear::find($id);
            if ($schoolYear->user_id != $user->id){
                return response()->json([
                    'status' => 403,
                    'message' => 'Unauthorized'
                ],403);
            }

            return response()->json([
                'status' => 200,
                'data' => $schoolYear
            ],200);
        }

        return response()->json([
            'status' => 404,
            'message' => 'Not founded'
        ],404);
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
    public function get_semesters_by_schoolYearId(string $id){
        $user = User::find(auth()->id());
        if ($user){
            $schoolYear = $user->school_years->find($id);
            if($schoolYear->user_id != $user->id){
                return response()->json([
                    'status' => 403,
                    'message' => 'Unauthorized'
                ],403);
            }
            $semesters = $schoolYear->semesters;
            return response()->json([
                'status' => 200,
                'data' => $semesters
             ],200);
        }
    }
    public function delete_semester(string $semesterId) {
        $user = auth()->user();

        if ($user) {
            $semester = Semester::find($semesterId);

            if ($semester) {
                // Xóa tất cả các bản ghi liên quan từ bảng type_tasks trước
                $semester->courses->each(function ($course) {
                    $course->tasks->each(function ($task) {
                        $task->type_task()->delete(); // Sửa thành type_task()
                    });
                });

                // Xóa tất cả các bản ghi liên quan từ bảng tasks trước
                $semester->courses->each(function ($course) {
                    $course->tasks()->delete();
                });

                // Xóa tất cả các bản ghi liên quan từ bảng school_classes trước
                $semester->courses->each(function ($course) {
                    $course->school_classes()->delete();
                });

                // Xóa tất cả các bản ghi liên quan từ bảng exams trước
                $semester->courses->each(function ($course) {
                    $course->exams()->delete();
                });

                // Xóa tất cả các khóa ngoại liên quan trong bảng courses
                $semester->courses()->delete();

                // Sau đó xóa semester
                $semester->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'Semester deleted successfully.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Semester not found.'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }
    public function destroy(string $schoolYearId) {
        $user = auth()->user();
        if ($user) {
            $schoolYear = SchoolYear::find($schoolYearId);

            if ($schoolYear) {
                $schoolYear->semesters->each(function ($semester) {
                    $semester->courses->each(function ($course) {
                        $course->tasks->each(function ($task) {
                            $task->type_task()->delete();
                            $task->delete();
                        });
                        $course->school_classes()->delete();
                        $course->exams()->delete();
                        $course->delete();
                    });
                });

                $schoolYear->semesters()->delete();

                $schoolYear->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'School year deleted successfully.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'School year not found.'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized'
            ], 403);
        }
    }



}
