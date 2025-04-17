<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\CategoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Activities
    Route::apiResource('activities', ActivityController::class);
    Route::get('activities/{activity}/participants', [ActivityController::class, 'participants']);
    
    // Requests
    Route::post('activities/{activity}/requests', [RequestController::class, 'store']);
    Route::patch('requests/{activityRequest}/status', [RequestController::class, 'updateStatus']);
    
    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/mark-read', [NotificationController::class, 'markAsRead']);
    
    // Categories
    Route::apiResource('categories', CategoryController::class)->only(['index', 'store']);
});