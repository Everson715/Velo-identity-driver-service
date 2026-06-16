<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// As rotas estão configuradas para o prefixo api/v1/ no bootstrap/app.php
// Exemplo de como essas rotas devem ser construídas:
Route::prefix('auth')->group(function () {
    Route::post('/login', function () {
        return response()->json(['message' => 'Login endpoint']);
    });
    // demais rotas de auth
});

Route::prefix('users')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'Users list']);
    });
    // demais rotas de usuarios
});

Route::prefix('me')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'Current user profile']);
    });
});
