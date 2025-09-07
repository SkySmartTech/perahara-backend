<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeraheraController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');





Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/service-types', [ServiceTypeController::class, 'types']);
Route::get('/services',  [ServiceController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',       [AuthController::class, 'me']);
    Route::post('/logout',  [AuthController::class, 'logout']);

    Route::post('/services', [ServiceController::class, 'store']);
});


// Public
Route::get('/peraheras', [PeraheraController::class, 'index']);
Route::get('/peraheras/{perahera}', [PeraheraController::class, 'show']);

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/peraheras', [PeraheraController::class, 'store']);
    Route::patch('/peraheras/{perahera}', [PeraheraController::class, 'update']);
    Route::delete('/peraheras/{perahera}', [PeraheraController::class, 'destroy']);
});