<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;

// Public Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Get current user profile
    Route::get('/me', [AuthController::class, 'me']);

    // Post Management Routes (all authenticated users)
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'getById']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'delete']);

    // User Management Routes (Admin Only)
    Route::get('/users', [UserController::class, 'index'])->middleware('abilities:view-users');
    Route::post('/users', [UserController::class, 'store'])->middleware('abilities:create-users');
    Route::get('/users/{id}', [UserController::class, 'getById'])->middleware('abilities:view-users');
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('abilities:update-users');
    Route::delete('/users/{id}', [UserController::class, 'delete'])->middleware('abilities:delete-users');
});
