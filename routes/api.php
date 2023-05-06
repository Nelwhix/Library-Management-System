<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LendingController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
   return response()->json([
       'message' => "pong @ " . \Illuminate\Support\Carbon::now() . " UTC"
   ]);
});

Route::middleware('guest')->group(function () {
    Route::post('/register', [UserController::class, 'store']);

    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function () {
        return response([
            'user' => auth()->user()
        ]);
    });

    Route::put('/edit-profile', [UserController::class, 'update']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::controller(LendingController::class)->group(function () {
        Route::post('/borrow-book', 'store');

        Route::put('/return-book', 'update');

        Route::get('/borrow/index', 'index');

        Route::get('/return/index', 'returnindex');
    });

    // users can subscribe to a plan
    Route::post('/plan/subscribe', [PlanController::class, 'store']);

    // users can see their previous subscriptions
    Route::get('/subscriptions/index', [PlanController::class, 'index']);

    Route::controller(BookController::class)->group(function () {
        Route::post('/book/add',  'store')->middleware(\App\Http\Middleware\Author::class);

        Route::get('/book/index', 'index');

        Route::put('/book/update',  'update');
    });


    Route::controller(AdminController::class)->group(function () {
        // admin can add a new plan
        Route::post('/plans/add', 'plan_store');

        // admin can see all plans on the db
        Route::get('/plans/index', 'plan_index');

        // admin can read one plan
        Route::post('/plans/show','plan_show');

        // admin can update plan
        Route::put('/plans/update', 'plan_update');

        // admin can delete a plan
        Route::delete('/plans/delete', 'plan_destroy');

        // admin can add a new access level
        Route::post('/accessLevel/add', 'access_level_store');

        // admin can see all access levels
        Route::get('/accessLevel/index', 'access_level_index');

        // admin can read one access level
        Route::post('/accessLevel/show', 'access_level_show');
    });
});
