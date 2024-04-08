<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReferenceDataController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::get('', [AuthController::class, 'checkToken']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::prefix('/project')->group(function () {
        Route::get('', [ProjectController::class, 'index']);
        Route::post('', [ProjectController::class, 'store']);
        Route::get('/{id}', [ProjectController::class, 'show']);
        Route::put('/{id}', [ProjectController::class, 'update']);
        Route::delete('/{id}', [ProjectController::class, 'destroy']);
    });

    Route::prefix('ticket')->group(function () {
        Route::get('', [TicketController::class, 'index']);
        Route::post('', [TicketController::class, 'store']);
        Route::get('/{id}', [TicketController::class, 'show']);
        Route::put('/{id}', [TicketController::class, 'update']);
        Route::put('/{id}/field', [TicketController::class, 'updateField']);
        Route::post('/{id}/assign', [TicketController::class, 'assignUser']);
        Route::delete('/{id}/unassign', [TicketController::class, 'unassignUser']);
        Route::delete('/{id}', [TicketController::class, 'destroy']);
    });

    Route::get('reference-data', [ReferenceDataController::class, 'index']);
});