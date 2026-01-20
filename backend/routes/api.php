<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LandController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful API routes for GeoOps Platform
| Following Laravel conventions and Clean Architecture
|
*/

Route::prefix('v1')->group(function () {
    
    // Public routes (no authentication required)
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    // Protected routes (require JWT authentication)
    Route::middleware(['auth:api', 'organization'])->group(function () {
        
        // Authentication
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });

        // Land Measurements
        Route::apiResource('lands', LandController::class);
        Route::get('map/lands/nearby', [LandController::class, 'nearby']);

        // Jobs
        Route::apiResource('jobs', JobController::class);
        Route::patch('jobs/{id}/status', [JobController::class, 'updateStatus']);
        Route::post('jobs/{id}/tracking', [JobController::class, 'addTracking']);
        Route::get('jobs/{id}/tracking', [JobController::class, 'getTracking']);

        // Invoices
        Route::apiResource('invoices', InvoiceController::class);
        Route::get('invoices/{id}/pdf', [InvoiceController::class, 'generatePdf']);
        Route::post('invoices/{id}/printed', [InvoiceController::class, 'markAsPrinted']);

        // Expenses
        Route::apiResource('expenses', ExpenseController::class);
        Route::get('expenses/summary', [ExpenseController::class, 'summary']);

        // Payments
        Route::apiResource('payments', PaymentController::class);

        // Synchronization
        Route::prefix('sync')->group(function () {
            Route::post('bulk', [SyncController::class, 'bulkSync']);
            Route::get('status', [SyncController::class, 'getStatus']);
            Route::post('conflicts/{id}/resolve', [SyncController::class, 'resolveConflict']);
        });

        // Maps & Location
        Route::prefix('map')->group(function () {
            Route::get('lands/nearby', [MapController::class, 'nearbyLands']);
            Route::get('drivers/active', [MapController::class, 'activeDrivers']);
        });

        // User Management
        Route::apiResource('users', UserController::class);
        Route::patch('users/{id}/deactivate', [UserController::class, 'deactivate']);
        Route::patch('users/{id}/activate', [UserController::class, 'activate']);

        // Machine Management
        Route::apiResource('machines', MachineController::class);

        // Subscription
        Route::prefix('subscription')->group(function () {
            Route::get('/', [SubscriptionController::class, 'current']);
            Route::get('features/{feature}', [SubscriptionController::class, 'checkFeature']);
        });

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('financial', [ReportController::class, 'financial']);
            Route::get('ledger', [ReportController::class, 'customerLedger']);
            Route::get('machines/{id}', [ReportController::class, 'machinePerformance']);
        });
    });

    // Health check endpoint (no authentication)
    Route::get('health', function () {
        return response()->json([
            'status' => 'ok',
            'database' => \DB::connection()->getPdo() ? 'ok' : 'error',
            'cache' => \Cache::has('health_check') ? 'ok' : 'error',
            'timestamp' => now()->toIso8601String(),
        ]);
    });
});
