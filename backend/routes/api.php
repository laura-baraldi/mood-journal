<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoodEntryController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mood-entries', [MoodEntryController::class, 'index']);
    Route::post('/mood-entries', [MoodEntryController::class, 'store']);
    Route::patch('/mood-entries/{id}', [MoodEntryController::class, 'update']);
    Route::delete('/mood-entries/{id}', [MoodEntryController::class, 'destroy']);
});
