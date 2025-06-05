<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProfileController;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);

    // Activities
    Route::apiResource('activities', ActivityController::class);
    Route::get('activities/{activity}/participants', [ActivityController::class, 'participants']);
    Route::get('users/{user}/activities', [ActivityController::class, 'userActivities']);
    Route::get('my-activities', [ActivityController::class, 'myActivitiesWithRequests']);
    Route::get('/activities/search/tags', [ActivityController::class, 'searchByTags']);
    Route::post('/activities/enhance-description', [ActivityController::class, 'enhanceDescription']);

    // Requests
    Route::post('activities/{activity}/requests', [RequestController::class, 'store']);
    Route::patch('requests/{activityRequest}/status', [RequestController::class, 'updateStatus']);
    Route::get('my-requests', [RequestController::class, 'myRequests']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/mark-read', [NotificationController::class, 'markAsRead']);

    // Categories
    Route::apiResource('categories', CategoryController::class)->only(['index', 'store']);
});

// Test route for Gemini AI tag generation
Route::post('/test/generate-tags', function (Request $request) {
    $request->validate([
        'title' => 'required|string',
        'description' => 'required|string'
    ]);

    $geminiService = app(App\Services\GeminiAIService::class);
    $tags = $geminiService->generateTags(
        $request->title,
        $request->description
    );

    return response()->json([
        'input' => [
            'title' => $request->title,
            'description' => $request->description
        ],
        'generated_tags' => $tags
    ]);
});
