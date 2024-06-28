<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);

    // check user if connected
    Route::get('/user', fn(Request $request) => $request->user());

    Route::middleware('roles:admin')->group(function () {
        Route::get('/tasks/deleted', [TaskController::class, 'deleted']);
    });
    
    Route::middleware('roles:admin,user')->group(function () {
        Route::get('/tasks',         [TaskController::class, 'index']);
        Route::post('/tasks',        [TaskController::class, 'store']);
        Route::get('/tasks/{id}',    [TaskController::class, 'show']);
        Route::put('/tasks/{id}',    [TaskController::class, 'update']);
        Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    });
});
