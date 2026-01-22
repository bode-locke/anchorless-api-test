<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // File management routes
    Route::get('/files', [FileController::class, 'index']);
    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
});
