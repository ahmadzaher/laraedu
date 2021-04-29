<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

Route::fallback(function(){
    return response()->json([
        'message' => 'Not found'], 404);
});
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('facebook', [AuthController::class, 'facebook']);

Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'userInfo']);
	Route::put('user', [AuthController::class, 'edit_profile']);
});
