<?php

use App\Http\Controllers\api\v1\QuestionController;
use App\Http\Controllers\api\v1\QuestionGroupController;
use App\Http\Controllers\api\v1\ForgotPasswordController;
use App\Http\Controllers\api\v1\ResetPasswordController;
use App\Http\Controllers\api\v1\QuizController;
use App\Http\Controllers\api\v1\SettingsController;
use App\Http\Controllers\api\v1\SummaryController;
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
Route::get('settings/general', [SettingsController::class, 'general_settings']);

Route::post('password/forgot-password', [ForgotPasswordController::class, 'forgot'])->name('passwords.sent');
Route::post('password/reset', [ResetPasswordController::class, 'sendResetResponse'])->name('passwords.reset');

Route::post('facebook', [AuthController::class, 'facebook']);

Route::middleware('auth:api')->group(function () {
    // Student quiz
    Route::middleware('role:student')->group(function() {
        Route::get('quizzes', [\App\Http\Controllers\api\v1\QuizController::class, 'all']);
        Route::get('student/quiz/{id}', [\App\Http\Controllers\api\v1\QuizController::class, 'get']);
        Route::get('categories', [\App\Http\Controllers\api\v1\CategoryController::class, 'all']);
    });
    // User Profile
    Route::get('userinfo', [AuthController::class, 'userinfo']);
    Route::post('userinfo', [AuthController::class, 'edit_profile']);
    Route::namespace('\App\Http\Controllers\api\v1')->group(function() {
        Route::apiResource('quiz', 'QuizController');
        Route::apiResource('branch', 'BranchController');
        Route::get('branches', [\App\Http\Controllers\api\v1\BranchController::class, 'all']);
        Route::apiResource('question', 'QuestionController');
        Route::apiResource('category', 'CategoryController');
        Route::get('quizzes/categories', [\App\Http\Controllers\api\v1\CategoryController::class, 'all']);
        Route::post('question/{id}', [QuestionController::class, 'update']);
        Route::apiResource('question_groups', 'QuestionGroupController');
        // Summary
        Route::apiResource('summary', 'SummaryController');
        Route::get('questions/groups', [QuestionGroupController::class, 'all']);
        Route::post('settings/general', [SettingsController::class, 'update_general_settings']);
    });

    Route::get('class/list', [ClassController::class, 'getclasses'])->middleware('can:view-class');
    Route::get('section/list', [SectionController::class, 'getSections'])->middleware('can:view-section');

    // Role
    Route::middleware('role:superadmin')->group(function () {
        Route::get('role/list', [RoleController::class, 'getRoles']);
        Route::get('permission/list', [RoleController::class, 'getPermissions']);
    });
    Route::post('role/add', [RoleController::class, 'store']);
    Route::post('role/edit/{id}', [RoleController::class, 'update']);
    Route::delete('role/delete/{id}', [RoleController::class, 'destroy']);

    // Student
    Route::get('student', [StudentController::class, 'getUsers']);
    Route::get('student/{id}', [StudentController::class, 'get']);
    Route::post('student', [StudentController::class, 'store']);
    Route::post('student/{id}', [StudentController::class, 'update']);
    Route::delete('student/{id}', [StudentController::class, 'destroy']);
    // Teacher
    Route::get('teacher', [TeacherController::class, 'getUsers']);
    Route::get('teacher/{id}', [TeacherController::class, 'get']);
    Route::post('teacher', [TeacherController::class, 'store']);
    Route::post('teacher/{id}', [TeacherController::class, 'update']);
    Route::delete('teacher/{id}', [TeacherController::class, 'destroy']);
    // User
    Route::get('user', [UserController::class, 'getUsers']);
    Route::get('user/statistics', [UserStatistics::class, 'users']);
    Route::get('user/{id}', [UserController::class, 'get']);
    Route::post('user', [UserController::class, 'store']);
    Route::post('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);

    // Bank
    Route::apiResource('bank', 'BankController');
});
