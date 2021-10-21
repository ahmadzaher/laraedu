<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\UserStatistics;
use App\Http\Controllers\api\StudentController;
use App\Http\Controllers\api\TeacherController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\SectionController;
use App\Http\Controllers\api\ClassController;

Route::fallback(function(){
    return response()->json([
        'message' => 'Not found'], 404);
});
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('facebook', [AuthController::class, 'facebook']);

Route::middleware('auth:api')->group(function () {
    // User Profile
    Route::get('user', [AuthController::class, 'userInfo']);
	Route::post('user', [AuthController::class, 'edit_profile']);
    Route::middleware('can:view-user')->group(function () {
        Route::get('user/list', [UserController::class, 'getUsers']);
        Route::get('user/statistics', [UserStatistics::class, 'users']);
    });
    
    Route::get('teacher/list', [TeacherController::class, 'getUsers'])->middleware(['can:view-teacher']);

    Route::get('class/list', [ClassController::class, 'getclasses'])->middleware('can:view-class');
    Route::get('section/list', [SectionController::class, 'getSections'])->middleware('can:view-section');
    
    
    Route::middleware('can:view-role')->group(function () {
        Route::get('role/list', [RoleController::class, 'getRoles']);
        Route::get('permission/list', [RoleController::class, 'getPermissions']);
    });
    // Student
    Route::get('student/list', [StudentController::class, 'getUsers'])->middleware('can:view-student');
    Route::post('student/add', [StudentController::class, 'store'])->middleware('can:create-student');

});
