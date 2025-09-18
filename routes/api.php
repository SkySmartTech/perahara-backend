<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\PeraheraController;
use App\Http\Controllers\ServiceTypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group.
|
*/

// -------------------- PUBLIC ROUTES --------------------

// Authentication
Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login',    [AuthController::class, 'login']);

// Service Types
Route::get('/service-types', [ServiceTypeController::class, 'types']);

// Services (Public)
Route::get('/services', [ServiceController::class, 'index']);        // All active services
Route::get('/services/{service}', [ServiceController::class, 'show']); // Single service

// Peraheras
Route::get('/peraheras', [PeraheraController::class, 'index']);
Route::get('/peraheras/{perahera}', [PeraheraController::class, 'show']);

// Blog Posts (Public)
Route::get('/blog-posts', [BlogPostController::class, 'index']);
Route::get('/blog-posts/{blogPost}', [BlogPostController::class, 'show']);


// -------------------- AUTHENTICATED ROUTES --------------------
Route::middleware('auth:sanctum')->group(function () {

    // User Info & Logout
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Service Provider Routes
    Route::post('/services', [ServiceController::class, 'store']);              // Add service
    Route::get('/provider/services', [ServiceController::class, 'providerServices']); // Provider dashboard

    // Peraheras (Protected)
    Route::post('/peraheras', [PeraheraController::class, 'store']);
    Route::patch('/peraheras/{perahera}', [PeraheraController::class, 'update']);
    Route::delete('/peraheras/{perahera}', [PeraheraController::class, 'destroy']);

    // Blog Posts (Protected)
    Route::post('/blog-posts', [BlogPostController::class, 'store']);
    Route::put('/blog-posts/{blogPost}', [BlogPostController::class, 'update']);
    Route::delete('/blog-posts/{blogPost}', [BlogPostController::class, 'destroy']);
});
