<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\PeraheraController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\AboutItemController;
use App\Http\Controllers\SubAboutItemController;
use App\Http\Controllers\SubAboutItemContentController;
use App\Http\Controllers\SubAboutItemContentDetailController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| Routes accessible without authentication
|
*/

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Service Types
Route::get('/service-types', [ServiceTypeController::class, 'types']);

// Services (public)
Route::get('/services',  [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

// Peraheras (public)
Route::get('/peraheras', [PeraheraController::class, 'index']);
Route::get('/peraheras/{perahera}', [PeraheraController::class, 'show']);   

// Blog posts (public)
Route::get('/blog-posts', [BlogPostController::class, 'index']);
Route::get('/blog-posts/latest', [BlogPostController::class, 'latest']);
Route::get('/blog-posts/{blogPost}', [BlogPostController::class, 'show']);

// About sections (public)
Route::get('/about-items', [AboutItemController::class, 'index']);
Route::get('/about-items/{aboutItem}', [AboutItemController::class, 'show']);

Route::get('/sub-about-items', [SubAboutItemController::class, 'index']);
Route::get('/sub-about-items/{subAboutItem}', [SubAboutItemController::class, 'show']);

Route::get('/sub-about-item-contents', [SubAboutItemContentController::class, 'index']);
Route::get('/sub-about-item-contents/{subAboutItemContent}', [SubAboutItemContentController::class, 'show']);

Route::get('/sub-about-item-content-details', [SubAboutItemContentDetailController::class, 'index']);
Route::get('/sub-about-item-content-details/{subAboutItemContentDetail}', [SubAboutItemContentDetailController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
|
| Routes requiring authentication via Sanctum
|
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Services (service providers can manage their own; admin can delete any)
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/my-services', [ServiceController::class, 'myServices']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

    // Peraheras (admin or organizer)
    Route::get('/my-peraheras', [PeraheraController::class, 'indexUser']);
    Route::post('/peraheras', [PeraheraController::class, 'store']);
    Route::put('/peraheras/{perahera}', [PeraheraController::class, 'update']);
    Route::delete('/peraheras/{perahera}', [PeraheraController::class, 'destroy']);

    // Blog posts (users manage their own; admin can delete any)
    Route::post('/blog-posts', [BlogPostController::class, 'store']);
    Route::put('/blog-posts/{blogPost}', [BlogPostController::class, 'update']);
    Route::delete('/blog-posts/{blogPost}', [BlogPostController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Admin-only Routes
|--------------------------------------------------------------------------
|
| Routes requiring authentication and admin middleware
|
*/
// Route::middleware(['auth:sanctum', 'admin'])->group(function () {
//     // About items
//     Route::post('/about-items', [AboutItemController::class, 'store']);
//     Route::put('/about-items/{aboutItem}', [AboutItemController::class, 'update']);
//     Route::delete('/about-items/{aboutItem}', [AboutItemController::class, 'destroy']);

//     // Sub about items
//     Route::post('/sub-about-items', [SubAboutItemController::class, 'store']);
//     Route::put('/sub-about-items/{subAboutItem}', [SubAboutItemController::class, 'update']);
//     Route::delete('/sub-about-items/{subAboutItem}', [SubAboutItemController::class, 'destroy']);

//     // Sub about item contents
//     Route::post('/sub-about-item-contents', [SubAboutItemContentController::class, 'store']);
//     Route::put('/sub-about-item-contents/{subAboutItemContent}', [SubAboutItemContentController::class, 'update']);
//     Route::delete('/sub-about-item-contents/{subAboutItemContent}', [SubAboutItemContentController::class, 'destroy']);

//     // Sub about item content details
//     Route::post('/sub-about-item-content-details', [SubAboutItemContentDetailController::class, 'store']);
//     Route::put('/sub-about-item-content-details/{subAboutItemContentDetail}', [SubAboutItemContentDetailController::class, 'update']);
//     Route::delete('/sub-about-item-content-details/{subAboutItemContentDetail}', [SubAboutItemContentDetailController::class, 'destroy']);
// });


use App\Http\Controllers\PasswordResetController;

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
