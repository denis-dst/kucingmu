<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\StoryApiController;
use App\Http\Controllers\Api\ExploreApiController;

/*
|--------------------------------------------------------------------------
| API Routes for KucingMU Social Mobile App (v1)
|--------------------------------------------------------------------------
*/

// Authentication
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Social Feed & Posts
Route::get('/posts', [FeedController::class, 'index']);
Route::get('/posts/{id}/comments', [FeedController::class, 'comments']);
Route::post('/posts/{id}/comments', [FeedController::class, 'addComment']);
Route::post('/posts/{id}/like', [FeedController::class, 'like']);
Route::post('/posts/{id}/save', [FeedController::class, 'save']);

// 24-Hour Ephemeral Stories
Route::get('/stories', [StoryApiController::class, 'index']);

// Explore & Search
Route::get('/social/explore', [ExploreApiController::class, 'explore']);
Route::get('/social/search', [ExploreApiController::class, 'search']);

// Notifications fallback
Route::get('/notifications', function () {
    return response()->json([
        'status' => 'success',
        'data' => [
            [
                'id' => '1',
                'type' => 'ktam_verified',
                'actor_name' => 'Admin KucingMu',
                'message' => 'Kartu KTAM Kucing Anda telah resmi diverifikasi & terbit di database KucingMu.',
                'time_ago' => 'Baru saja',
            ],
        ],
    ]);
});

// Conversations fallback
Route::get('/social/conversations', function () {
    return response()->json([
        'status' => 'success',
        'data' => [],
    ]);
});
