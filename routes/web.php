<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Permissions\HasPermissionsTrait;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/addroles', 'PermissionController@Permission');


//
Route::get('user', [UserController::class, 'index'])->name('users');
Route::get('user/list', [UserController::class, 'getUsers'])->name('user.list');
Route::get('user/add', [UserController::class, 'add'])->name('user.add');
Route::post('user', [UserController::class, 'store'])->name('user.store');
Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::put('user/edit/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');


Route::get('role', [RoleController::class, 'index'])->name('roles');
Route::get('role/list', [RoleController::class, 'getRoles'])->name('role.list');
Route::get('role/add', [RoleController::class, 'add'])->name('role.add');
Route::post('role', [RoleController::class, 'store'])->name('role.store');
Route::get('role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
Route::put('role/edit/{id}', [RoleController::class, 'update'])->name('role.update');
Route::delete('role/delete/{id}', [RoleController::class, 'destroy'])->name('role.destroy');


// Facebook login

Route::get('facebook/redirect', [LoginController::class, 'facebook'])->name('facebook.login');
Route::get('facebook/callback', [LoginController::class, 'facebookCallback']);

// Google login

Route::get('google/redirect', [LoginController::class, 'google'])->name('google.login');
Route::get('google/callback', [LoginController::class, 'googleCallback']);

Route::group(['middleware' => 'role:superadmin'], function() {


    Route::get('permission', [PermissionController::class, 'index'])->name('permissions');
    Route::get('permission/list', [PermissionController::class, 'getPermissions'])->name('permission.list');


});
