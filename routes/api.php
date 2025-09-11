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
| Public routes (viewable without auth)
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/service-types', [ServiceTypeController::class, 'types']);

Route::get('/services',  [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']); // public show

// Peraheras public
Route::get('/peraheras', [PeraheraController::class, 'index']);
Route::get('/peraheras/{perahera}', [PeraheraController::class, 'show']);

// Blog posts public
Route::get('/blog-posts', [BlogPostController::class, 'index']);
Route::get('/blog-posts/{blogPost}', [BlogPostController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Protected routes (auth:sanctum)
|--------------------------------------------------------------------------
|
| Authenticated actions: create/update/delete, profile, logout, etc.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Services (service providers can create/update/delete their own; admin can delete)
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/my-services', [ServiceController::class, 'myServices']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

    // Peraheras (admin or organizer)
    Route::post('/peraheras', [PeraheraController::class, 'store']);
    Route::patch('/peraheras/{perahera}', [PeraheraController::class, 'update']);
    Route::delete('/peraheras/{perahera}', [PeraheraController::class, 'destroy']);

    // Blog posts (users create/update/delete their own; admin can delete any)
    Route::post('/blog-posts', [BlogPostController::class, 'store']);
    Route::put('/blog-posts/{blogPost}', [BlogPostController::class, 'update']);
    Route::delete('/blog-posts/{blogPost}', [BlogPostController::class, 'destroy']);
});
