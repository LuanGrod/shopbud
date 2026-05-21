<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(["throttle:global", "auth:sanctum"])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::prefix("auth")->group(function () {
    Route::post("/register", [AuthController::class, 'register']);
    Route::post("/login", [AuthController::class, 'login'])->middleware("throttle:login");
    Route::post("/logout", [AuthController::class, 'logout']);
});
