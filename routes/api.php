<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login',    [AuthController::class, 'login'])->name('login');


Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // check user if connected
    Route::get('/user', fn(Request $request) => $request->user())->name('checkUser');

    Route::middleware('roles:admin')->group(function () {
        Route::get('/tasks/deleted', [TaskController::class, 'deleted'])->name('deleted');
    });
    
    Route::middleware('roles:admin,user')->group(function () {
        Route::get('/tasks',         [TaskController::class, 'index'])->name('index');
        Route::post('/tasks',        [TaskController::class, 'store'])->name('store');
        Route::get('/tasks/{id}',    [TaskController::class, 'show'])->name('show');
        Route::put('/tasks/{id}',    [TaskController::class, 'update'])->name('update');
        Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('destroy');
    });
});
