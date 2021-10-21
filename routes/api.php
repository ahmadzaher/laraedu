<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\UserStatistics;
use App\Http\Controllers\api\StudentController;
use App\Http\Controllers\api\TeacherController;

Route::fallback(function(){
    return response()->json([
        'message' => 'Not found'], 404);
});
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('facebook', [AuthController::class, 'facebook']);

Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'userInfo']);
	Route::post('user', [AuthController::class, 'edit_profile']);
    Route::middleware('can:view-user')->group(function () {
        Route::get('user/list', [UserController::class, 'getUsers']);
        Route::get('user/statistics', [UserStatistics::class, 'users']);
    });
    Route::middleware('can:view-teacher')->group(function () {
        Route::get('teacher/list', [TeacherController::class, 'getUsers']);
    });
    Route::middleware('can:view-student')->group(function () {
        Route::get('student/list', [StudentController::class, 'getUsers']);
    });

});
