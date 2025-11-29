<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;

Route::prefix('v1')->middleware('throttle:api')->group(function(){
    //Выдача публичных данных
    Route::get('/cities', [CityController::class, 'getAll']);
    Route::get('/users', [UserController::class, 'getAll']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('api.password.reset');
    
    //Для аутентифицированных пользователей
    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::apiResource('tasks', TaskController::class);

        Route::get('/verification-notification', [VerifyEmailController::class, 'sendNotification']);
        Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])
        ->middleware(['signed'])->name('api.verification.verify');

        Route::get('/users/{id}', [UserController::class, 'getUser'])
            ->middleware(['verified']);

        Route::patch('/profile/update', [UserProfileController::class, 'update']);
        Route::post('/logout', [AuthController::class, 'logout']);

    });
});
