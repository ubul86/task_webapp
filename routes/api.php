<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::put('/tasks/set-completed/{id}', [TaskController::class, 'setCompleted']);
Route::put('/tasks/set-bulk-completed', [TaskController::class, 'setBulkCompleted']);
Route::delete('/tasks/bulk-destroy', [TaskController::class, 'bulkDestroy']);

Route::apiResource('tasks', TaskController::class);

