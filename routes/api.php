<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);        // list tasks
    Route::post('/tasks', [TaskController::class, 'store']);       // create task
    Route::get('/tasks/{task}', [TaskController::class, 'show']);  // show task
    Route::put('/tasks/{task}', [TaskController::class, 'update']); // update task
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']); // soft delete
});