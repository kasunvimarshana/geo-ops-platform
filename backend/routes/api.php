<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FieldJobController;
use App\Http\Controllers\Api\V1\LandController;
use App\Http\Controllers\Api\V1\MeasurementController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\TrackingController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('v1')->group(function () {
    // Public authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        // Protected authentication routes
        Route::middleware(['jwt.auth'])->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // Protected API routes (require authentication and organization isolation)
    Route::middleware(['jwt.auth', 'organization.isolation'])->group(function () {
        // Land endpoints
        Route::prefix('lands')->group(function () {
            Route::get('/', [LandController::class, 'index']);
            Route::post('/', [LandController::class, 'store']);
            Route::get('/{id}', [LandController::class, 'show']);
            Route::put('/{id}', [LandController::class, 'update']);
            Route::delete('/{id}', [LandController::class, 'destroy']);
            Route::get('/{id}/measurements', [LandController::class, 'measurements']);
        });

        // Measurement endpoints
        Route::prefix('measurements')->group(function () {
            Route::get('/', [MeasurementController::class, 'index']);
            Route::post('/', [MeasurementController::class, 'store']);
            Route::get('/{id}', [MeasurementController::class, 'show']);
            Route::post('/batch', [MeasurementController::class, 'batchStore']);
        });

        // Job endpoints
        Route::prefix('jobs')->group(function () {
            Route::get('/', [FieldJobController::class, 'index']);
            Route::post('/', [FieldJobController::class, 'store']);
            Route::get('/{id}', [FieldJobController::class, 'show']);
            Route::put('/{id}', [FieldJobController::class, 'update']);
            Route::delete('/{id}', [FieldJobController::class, 'destroy']);
            Route::patch('/{id}/assign', [FieldJobController::class, 'assign']);
            Route::patch('/{id}/start', [FieldJobController::class, 'start']);
            Route::patch('/{id}/complete', [FieldJobController::class, 'complete']);
        });

        // Invoice endpoints
        Route::prefix('invoices')->group(function () {
            Route::get('/', [InvoiceController::class, 'index']);
            Route::post('/', [InvoiceController::class, 'store']);
            Route::post('/from-job', [InvoiceController::class, 'createFromJob']);
            Route::get('/{id}', [InvoiceController::class, 'show']);
            Route::put('/{id}', [InvoiceController::class, 'update']);
            Route::delete('/{id}', [InvoiceController::class, 'destroy']);
            Route::get('/{id}/pdf', [InvoiceController::class, 'generatePDF']);
        });

        // Payment endpoints
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index']);
            Route::post('/', [PaymentController::class, 'store']);
            Route::get('/{id}', [PaymentController::class, 'show']);
            Route::delete('/{id}', [PaymentController::class, 'destroy']);
        });

        // Expense endpoints
        Route::prefix('expenses')->group(function () {
            Route::get('/', [ExpenseController::class, 'index']);
            Route::post('/', [ExpenseController::class, 'store']);
            Route::get('/totals', [ExpenseController::class, 'totals']);
            Route::get('/{id}', [ExpenseController::class, 'show']);
            Route::put('/{id}', [ExpenseController::class, 'update']);
            Route::delete('/{id}', [ExpenseController::class, 'destroy']);
        });

        // Tracking endpoints
        Route::prefix('tracking')->group(function () {
            Route::post('/', [TrackingController::class, 'store']);
            Route::get('/live', [TrackingController::class, 'getLiveLocations']);
            Route::get('/user/{userId}', [TrackingController::class, 'getUserTracking']);
            Route::get('/user/{userId}/stats', [TrackingController::class, 'getUserStats']);
            Route::get('/job/{jobId}', [TrackingController::class, 'getJobTracking']);
        });

        // Subscription endpoints
        Route::prefix('subscriptions')->group(function () {
            Route::get('/packages', [SubscriptionController::class, 'packages']);
            Route::get('/current', [SubscriptionController::class, 'current']);
            Route::post('/check-limit', [SubscriptionController::class, 'checkLimit']);
        });
        
        // Routes that require specific roles
        Route::middleware(['role:admin'])->group(function () {
            // Admin-only routes
        });

        Route::middleware(['role:admin,owner'])->group(function () {
            // Admin and Owner routes
        });
    });
});
