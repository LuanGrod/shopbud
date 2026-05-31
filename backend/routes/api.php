<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:global', 'auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('templates', TemplateController::class);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/logout', [AuthController::class, 'logout']);
});
