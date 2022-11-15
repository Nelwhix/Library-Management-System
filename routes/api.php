<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LendingController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::post('/register', [UserController::class, 'store']);

    Route::post('login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response([
            'user' => auth()->user()
        ]);
    });

    Route::put('/profile/edit', [UserController::class, 'update']);

    Route::post('/borrow-book', [LendingController::class, 'store']);

    Route::put('/return-book', [LendingController::class, 'update']);

    Route::post('/logout', [UserController::class, 'logout']);

    // users can see how many books borrowed (by self)
    Route::get('/borrow/index', [LendingController::class, 'index']);

    // users can see how many books returned (by self)
    Route::get('/return/index', [LendingController::class, 'returnindex']);

    // users can subscribe to a plan
    Route::post('/plan/subscribe', [PlanController::class, 'store']);

    // users can see their previous subscriptions
    Route::get('/subscriptions/index', [PlanController::class, 'index']);

    // author can add new book
    Route::post('/book/add', [BookController::class, 'store']);

    // author can see his books
    Route::get('/books/index', [BookController::class, 'index']);

    // author can update his book
    Route::put('/books/update', [BookController::class, 'update']);

    // admin can add a new plan
    Route::post('/plans/add', [AdminController::class, 'plan_store']);

    // admin can see all plans on the db
    Route::get('/plans/index', [AdminController::class, 'plan_index']);

    // admin can read one plan
    Route::post('/plans/show', [AdminController::class, 'plan_show']);

    // admin can update plan
    Route::put('/plans/update', [AdminController::class, 'plan_update']);

    // admin can delete a plan
    Route::delete('/plans/delete', [AdminController::class, 'plan_destroy']);

    // admin can add a new access level
    Route::post('/accessLevel/add', [AdminController::class, 'access_level_store']);

    // admin can see all access levels
    Route::get('/accessLevel/index', [AdminController::class, 'access_level_index']);

    // admin can read one access level
    Route::post('/accessLevel/show', [AdminController::class, 'access_level_show']);
});
