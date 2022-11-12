<?php

use App\Http\Controllers\LendingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::post('/register', [UserController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/profile/edit', [UserController::class, 'update']);
    Route::post('/borrow-book', [LendingController::class, 'store']);
    Route::put('/return-book', [LendingController::class, 'update']);
});
