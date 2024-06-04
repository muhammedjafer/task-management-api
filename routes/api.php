<?php

use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;

Route::middleware('auth:sanctum')->group(function () {

    Route::resource('user', UserController::class)->only(['store', 'update', 'destroy'])->middleware('CheckRole:'.RoleEnum::PRODUCT_OWNER->value);
});

Route::post('login', [AuthController::class, 'login'])->middleware('guest');