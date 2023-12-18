<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\TaskController;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\SchoolYearController;
use PhpParser\Builder\Class_;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::apiResource('dashboard',DashboardController::class)->only(['get_today_detail']);

Route::post ('/register',[AuthController::class,'register']);

Route::post ('/verify/{user}',[AuthController::class,'verify']);

Route::post('/login',[AuthController::class,'login']);

Route::get('/dashboard/get_today_detail',[DashboardController::class,'get_today_detail']);

Route::get('/dashboard/get_classes_exams',[DashboardController::class,'get_classes_exams']);

Route::get('/dashboard/get_due_tasks',[DashboardController::class,'get_due_tasks']);

Route::get('/dashboard/get_overdue_tasks',[DashboardController::class,'get_overdue_tasks']);

Route::apiResources([
    'tasks' => TaskController::class
]);


Route::post('/schedules/store_class',[ScheduleController::class,'store_class']);
Route::get('/schedules/get_class_list',[ScheduleController::class,'get_class_list']);
Route::delete('/calendars/destroy_class/{id}',[CalendarController::class,'destroy']);
Route::put('/calendars/update_class/{id}',[CalendarController::class,'update']);
Route::get('/calendars/get_list_classes',[CalendarController::class,'get_list_classes']);
Route::get('/schedules/get_course_detail/{id}',[ScheduleController::class,'get_course_detail']);

Route::post('schedules/store_course',[ScheduleController::class,'store_course']);

Route::post('schedules/store_semester',[ScheduleController::class,'store_semester']);

Route::delete('schedules/destroy_course/{id}',[ScheduleController::class,'destroy_course']);

Route::get('/exams', [ExamController::class, 'index']);

Route::get('/exams/{exam}', [ExamController::class, 'show']);

Route::post('/exams', [ExamController::class, 'store']);

Route::put('/exams/{exam}', [ExamController::class, 'update']);

Route::delete('/exams/{exam}', [ExamController::class, 'destroy']);

Route::get('/calendars/get_detail_class/{id}',[CalendarController::class,'get_detail_class']);

//schooolyear
Route::post('/school-years', [SchoolYearController::class, 'store']);
Route::put('/school-years/{id}', [SchoolYearController::class, 'update']);
Route::post('/school-years/{id}', [SchoolYearController::class, 'detroy']);

Route::apiResource('settings', SettingController::class)->only('update');

