<?php

use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\User\UserController;

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('CheckRole:'.RoleEnum::PRODUCT_OWNER->value)->group(function () {

        //user routes
        Route::resource('user', UserController::class)->only(['store', 'update', 'destroy']);
    
        //task route
        Route::resource('task', TaskController::class)->only(['store', 'update', 'destroy']);
    });

    //task route
    Route::get('task-list', [TaskController::class, 'taskList']);
    Route::get('task/{task}', [TaskController::class, 'taskDetails']);
    Route::get('batch/{id}/progress', [TaskController::class, 'getProgress']);
    Route::put('change-status/{task}', [TaskController::class, 'changeStatus'])->middleware('check_role_for_status_change');

    //auth route
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('guest')->group(function () {

    //auth route
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
});