<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MeController;

use App\Application\Http\Controllers\DriverIdentityController;

// Users Endpoints -> Ficará automaticamente como: /api/v1/users/...
Route::prefix('users')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/verify-email', [UserController::class, 'verifyEmail']);
    Route::post('/resend-verification', [UserController::class, 'resendVerification']);
});

// Drivers Endpoints
Route::prefix('drivers')->group(function () {
    Route::post('/{driverId}/approve', [DriverIdentityController::class, 'approveDriver']);
});

// Auth Endpoints -> Ficará automaticamente como: /api/v1/auth/...
Route::prefix('auth')->group(function () {
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::patch('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/verify', [AuthController::class, 'verify'])->middleware('auth:api');
    Route::get('/certs', [AuthController::class, 'certs']);
    Route::get('/public-key', [AuthController::class, 'publicKey']);
});

// Me Endpoints -> Ficará automaticamente como: /api/v1/me/...
Route::prefix('me')->middleware('auth:api')->group(function () {
    Route::get('/', [MeController::class, 'show']);
    Route::patch('/', [MeController::class, 'update']);
    Route::post('/avatar', [MeController::class, 'avatar']);
    Route::put('/password', [MeController::class, 'updatePassword']);
    Route::get('/sessions', [MeController::class, 'sessions']);
    Route::delete('/sessions/{id}', [MeController::class, 'revokeSession']);
    Route::delete('/account', [MeController::class, 'destroy']);
});
