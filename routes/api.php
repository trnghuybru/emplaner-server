<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\TaskController;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/schedules/get_class_list',[ScheduleController::class,'get_class_list']);
