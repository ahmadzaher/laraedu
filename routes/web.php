<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;;
use App\Http\Controllers\exam\ExamController;
use App\Http\Controllers\exam\ExamGradeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\frontend;

Auth::routes(['register' => false]);

//Route::any('/', function () {
////    echo env('APP_URL') . '/build'; exit;
//    return redirect(env('BASE_URL') . '/build');
//});
// Frontend Routes
Route::group(['domain' => 'namedomain.com', 'middleware' => 'frontendActive'], function() {
    Route::get('/', 'FrontendController@index')->name('frontend');
});

//Dashboard
Route::get('/dashboard', 'HomeController@index')->name('dashboard');
Route::get('/home', 'HomeController@index')->name('home');


//User routes
Route::get('user', [UserController::class, 'index'])->name('users');
Route::get('user/list', [UserController::class, 'getUsers'])->name('user.list');
Route::get('user/add', [UserController::class, 'add'])->name('user.add');
Route::post('user', [UserController::class, 'store'])->name('user.store');
Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::put('user/edit/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');
Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

// Student routes
Route::get('student', [StudentController::class, 'index'])->name('students');
Route::get('student/list', [StudentController::class, 'getUsers'])->name('student.list');
Route::get('student/add', [StudentController::class, 'add'])->name('student.add');
Route::post('student', [StudentController::class, 'store'])->name('student.store');
Route::get('student/edit/{id}', [StudentController::class, 'edit'])->name('student.edit');
Route::put('student/edit/{id}', [StudentController::class, 'update'])->name('student.update');
Route::delete('student/delete/{id}', [StudentController::class, 'destroy'])->name('student.destroy');


// Teacher routes
Route::get('teacher', [TeacherController::class, 'index'])->name('teachers');
Route::get('teacher/list', [TeacherController::class, 'getUsers'])->name('teacher.list');
Route::get('teacher/add', [TeacherController::class, 'add'])->name('teacher.add');
Route::post('teacher', [TeacherController::class, 'store'])->name('teacher.store');
Route::get('teacher/edit/{id}', [TeacherController::class, 'edit'])->name('teacher.edit');
Route::put('teacher/edit/{id}', [TeacherController::class, 'update'])->name('teacher.update');
Route::delete('teacher/delete/{id}', [TeacherController::class, 'destroy'])->name('teacher.destroy');

//Teacher allocations
Route::get('teacher_allocation', [TeacherController::class, 'teacher_allocation'])->name('teacher_allocation');
Route::get('teacher_allocations', [TeacherController::class, 'getAllocations'])->name('allocation.list');
Route::get('allocation/add', [TeacherController::class, 'add_allocation'])->name('allocation.add');
Route::post('allocation', [TeacherController::class, 'store_allocation'])->name('allocation.store');
Route::get('allocation/edit/{id}', [TeacherController::class, 'edit_allocation'])->name('allocation.edit');
Route::put('allocation/edit/{id}', [TeacherController::class, 'update_allocation'])->name('allocation.update');
Route::delete('allocation/delete/{id}', [TeacherController::class, 'destroy_allocation'])->name('allocation.destroy');

// Role routes
Route::get('role', [RoleController::class, 'index'])->name('roles');
Route::get('role/list', [RoleController::class, 'getRoles'])->name('role.list');
Route::get('role/add', [RoleController::class, 'add'])->name('role.add');
Route::post('role', [RoleController::class, 'store'])->name('role.store');
Route::get('role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
Route::put('role/edit/{id}', [RoleController::class, 'update'])->name('role.update');
Route::delete('role/delete/{id}', [RoleController::class, 'destroy'])->name('role.destroy');

// Class routes
Route::get('class', [ClassController::class, 'index'])->name('classes');
Route::get('class/list', [ClassController::class, 'getClasses'])->name('class.list');
Route::get('class/add', [ClassController::class, 'add'])->name('class.add');
Route::post('class', [ClassController::class, 'store'])->name('class.store');
Route::get('class/edit/{id}', [ClassController::class, 'edit'])->name('class.edit');
Route::get('class/sections/{id}', [ClassController::class, 'getClassSections'])->name('class.sections');
Route::put('class/edit/{id}', [ClassController::class, 'update'])->name('class.update');
Route::delete('class/delete/{id}', [ClassController::class, 'destroy'])->name('class.destroy');

// Section routes
Route::get('section', [SectionController::class, 'index'])->name('sections');
Route::get('section/list', [SectionController::class, 'getSections'])->name('section.list');
Route::get('section/add', [SectionController::class, 'add'])->name('section.add');
Route::post('section', [SectionController::class, 'store'])->name('section.store');
Route::get('section/edit/{id}', [SectionController::class, 'edit'])->name('section.edit');
Route::put('section/edit/{id}', [SectionController::class, 'update'])->name('section.update');
Route::delete('section/delete/{id}', [SectionController::class, 'destroy'])->name('section.destroy');

// Subject routes
Route::get('subject', [SubjectController::class, 'index'])->name('subjects');
Route::get('subject/list', [SubjectController::class, 'getSubjects'])->name('subject.list');
Route::get('subject/add', [SubjectController::class, 'add'])->name('subject.add');
Route::post('subject', [SubjectController::class, 'store'])->name('subject.store');
Route::get('subject/edit/{id}', [SubjectController::class, 'edit'])->name('subject.edit');
Route::put('subject/edit/{id}', [SubjectController::class, 'update'])->name('subject.update');
Route::delete('subject/delete/{id}', [SubjectController::class, 'destroy'])->name('subject.destroy');

// Exam routes
Route::get('exam', [ExamController::class, 'index'])->name('exams');
Route::get('exam/list', [ExamController::class, 'getExams'])->name('exam.list');
Route::get('exam/add', [ExamController::class, 'add'])->name('exam.add');
Route::post('exam', [ExamController::class, 'store'])->name('exam.store');
Route::get('exam/edit/{id}', [ExamController::class, 'edit'])->name('exam.edit');
Route::put('exam/edit/{id}', [ExamController::class, 'update'])->name('exam.update');
Route::delete('exam/delete/{id}', [ExamController::class, 'destroy'])->name('exam.destroy');

// Exam routes
Route::get('exam_grade', [ExamGradeController::class, 'index'])->name('exam_grades');
Route::get('exam_grade/list', [ExamGradeController::class, 'getExamGrades'])->name('exam_grade.list');
Route::get('exam_grade/add', [ExamGradeController::class, 'add'])->name('exam_grade.add');
Route::post('exam_grade', [ExamGradeController::class, 'store'])->name('exam_grade.store');
Route::get('exam_grade/edit/{id}', [ExamGradeController::class, 'edit'])->name('exam_grade.edit');
Route::put('exam_grade/edit/{id}', [ExamGradeController::class, 'update'])->name('exam_grade.update');
Route::delete('exam_grade/delete/{id}', [ExamGradeController::class, 'destroy'])->name('exam_grade.destroy');


// Department routes
Route::get('department', [DepartmentController::class, 'index'])->name('departments');
Route::get('department/list', [DepartmentController::class, 'getDepartments'])->name('department.list');
Route::get('department/add', [DepartmentController::class, 'add'])->name('department.add');
Route::post('department', [DepartmentController::class, 'store'])->name('department.store');
Route::get('department/edit/{id}', [DepartmentController::class, 'edit'])->name('department.edit');
Route::put('department/edit/{id}', [DepartmentController::class, 'update'])->name('department.update');
Route::delete('department/delete/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');

// Frontend routes
Route::get('frontend/settings', 'frontend\SettingsController@index')->name('frontend_settings');
Route::post('frontend/settings/update', 'frontend\SettingsController@update')->name('frontend_settings.update');
Route::get('frontend/hero_area', 'frontend\HeroAreaController@index')->name('hero_area');
Route::post('frontend/hero_area/update', 'frontend\HeroAreaController@update')->name('hero_area.update');
Route::get('frontend/menu', 'frontend\MenuController@index')->name('frontend_menu');


Route::group(['middleware' => 'role:superadmin'], function() {


    Route::get('permission', [PermissionController::class, 'index'])->name('permissions');
    Route::get('permission/list', [PermissionController::class, 'getPermissions'])->name('permission.list');


});
