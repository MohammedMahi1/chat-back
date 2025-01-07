<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user_store',[UserController::class,'store']);
Route::post('/user_login',[UserController::class,'login']);
Route::delete('/user_logout',[UserController::class,'destroy']);
Route::get('/user',[userController::class,'index']);
Route::post('/user_profile_image',[UserController::class,'updateImgProfile']);