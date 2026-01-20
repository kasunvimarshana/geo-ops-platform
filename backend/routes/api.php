<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FieldController;
use App\Http\Controllers\Api\V1\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->group(function () {
    
    // Public routes with rate limiting
    Route::middleware(['throttle:' . env('AUTH_RATE_LIMIT_PER_MINUTE', 5) . ',1'])->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/auth/login', [AuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        
        // Auth routes
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);

        // Field routes
        Route::apiResource('fields', FieldController::class);
        Route::get('fields/{id}/report', [FieldController::class, 'report']);

        // Job routes
        Route::apiResource('jobs', JobController::class);
        Route::get('jobs/{id}/report', [JobController::class, 'report']);
    });
});

