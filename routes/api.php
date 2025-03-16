<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/password-reset', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);  // Fetch articles
    Route::get('/articles/{id}', [ArticleController::class, 'show']);  // Fetch single article

    Route::post('/preferences', [UserPreferenceController::class, 'store']);   // Save preferences
    Route::get('/preferences', [UserPreferenceController::class, 'index']);    // Get preferences
    Route::get('/news-feed', [UserPreferenceController::class, 'newsFeed']);   // Get personalized news feed
});
