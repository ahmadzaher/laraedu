<?php
use Illuminate\Support\Facades\Route;

Route::fallback(function(){
    return response()->json([
        'message' => 'Not found'], 404);
});
