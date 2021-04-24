<?php

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Permissions\HasPermissionsTrait;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/posts', function () {
    return view('posts.index');
});

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


Route::get('permission', [PermissionController::class, 'index'])->name('permissions');
Route::get('permission/list', [PermissionController::class, 'getPermissions'])->name('permission.list');

// Facebook login

Route::get('facebook/redirect', [LoginController::class, 'facebook'])->name('facebook.login');
Route::get('facebook/callback', [LoginController::class, 'facebookCallback']);

// Google login

Route::get('google/redirect', [LoginController::class, 'google'])->name('google.login');
Route::get('google/callback', [LoginController::class, 'googleCallback']);


/*
 *  If you already have a valid access token for a user, you can retrieve their details using Socialite's userFromToken method:
 * $user = Socialite::driver('facebook')->userFromToken($token);
 *
 *
 *
 *
 *
 *
 *
 * The stateless method may be used to disable session state verification. This is useful when adding social authentication to an API:
 * return Socialite::driver('google')->stateless()->user();
 * */
Route::group(['middleware' => 'role:developer'], function() {

    Route::get('/admin', function() {

        return 'Welcome Admin';

    });

});
