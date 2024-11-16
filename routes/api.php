<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

Route::put('/tasks/toggle-completed/{id}', [TaskController::class, 'toggleCompleted']);
Route::put('/tasks/set-bulk-completed', [TaskController::class, 'setBulkCompleted']);
Route::put('/tasks/add-used-time-to-task', [TaskController::class, 'addUsedTimeToTask']);
Route::delete('/tasks/bulk-destroy', [TaskController::class, 'bulkDestroy']);

Route::apiResource('tasks', TaskController::class);
Route::apiResource('users', UserController::class);

