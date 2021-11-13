<?php

use App\Http\Controllers\api\v1\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\UserController;
use App\Http\Controllers\api\v1\UserStatistics;
use App\Http\Controllers\api\v1\StudentController;
use App\Http\Controllers\api\v1\TeacherController;
use App\Http\Controllers\api\v1\RoleController;
use App\Http\Controllers\api\v1\SectionController;
use App\Http\Controllers\api\v1\ClassController;

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
    Route::namespace('\App\Http\Controllers\api\v1')->middleware('role:superadmin')->group(function() {
       Route::apiResource('quiz', 'QuizController');
       Route::post('quiz/questions/{quiz_id}', [QuizController::class, 'questions_store']);

    });

    Route::get('class/list', [ClassController::class, 'getclasses'])->middleware('can:view-class');
    Route::get('section/list', [SectionController::class, 'getSections'])->middleware('can:view-section');

    // Role
    Route::middleware('role:superadmin')->group(function () {
        Route::get('role/list', [RoleController::class, 'getRoles']);
        Route::get('permission/list', [RoleController::class, 'getPermissions']);
    });
    Route::post('role/add', [RoleController::class, 'store'])->middleware(['role:superadmin']);
    Route::post('role/edit/{id}', [RoleController::class, 'update'])->middleware('role:superadmin');
    Route::delete('role/delete/{id}', [RoleController::class, 'destroy'])->middleware('role:superadmin');

    // Student
    Route::get('student/list', [StudentController::class, 'getUsers'])->middleware('can:view-student');
    Route::get('student/{id}', [StudentController::class, 'get'])->middleware('can:view-student');
    Route::post('student/add', [StudentController::class, 'store'])->middleware('can:create-student');
    Route::post('student/edit/{id}', [StudentController::class, 'update'])->middleware('can:edit-student');
    Route::delete('student/delete/{id}', [StudentController::class, 'destroy'])->middleware('can:delete-student');
    // Teacher
    Route::get('teacher/list', [TeacherController::class, 'getUsers'])->middleware(['can:view-teacher']);
    Route::get('teacher/{id}', [TeacherController::class, 'get'])->middleware('can:view-teacher');
    Route::post('teacher/add', [TeacherController::class, 'store'])->middleware(['can:create-teacher']);
    Route::post('teacher/edit/{id}', [TeacherController::class, 'update'])->middleware('can:edit-teacher');
    Route::delete('teacher/delete/{id}', [TeacherController::class, 'destroy'])->middleware('can:delete-teacher');
    // User
    Route::middleware('can:view-user')->group(function () {
        Route::get('user/list', [UserController::class, 'getUsers']);
        Route::get('user/statistics', [UserStatistics::class, 'users']);
        Route::get('user/{id}', [UserController::class, 'get']);
    });
    Route::post('user/add', [UserController::class, 'store'])->middleware(['can:create-user']);
    Route::post('user/edit/{id}', [UserController::class, 'update'])->middleware('can:edit-user');
    Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->middleware('can:delete-user');


});
