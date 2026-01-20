<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeasurementController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\SyncController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes (require JWT authentication)
Route::middleware('auth:api')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    // Land Measurements
    Route::apiResource('measurements', MeasurementController::class);
    
    // Jobs
    Route::apiResource('jobs', JobController::class);
    Route::post('jobs/{id}/status', [JobController::class, 'updateStatus']);
    Route::post('jobs/{id}/assign', [JobController::class, 'assign']);
    
    // Tracking
    Route::prefix('tracking')->group(function () {
        Route::post('/', [TrackingController::class, 'store']);
        Route::get('drivers/{driverId}', [TrackingController::class, 'driverHistory']);
        Route::get('jobs/{jobId}', [TrackingController::class, 'jobHistory']);
        Route::get('active', [TrackingController::class, 'activeDrivers']);
    });
    
    // Customers
    Route::apiResource('customers', CustomerController::class);
    Route::get('customers/{id}/statistics', [CustomerController::class, 'statistics']);
    
    // Drivers
    Route::apiResource('drivers', DriverController::class);
    Route::get('drivers/{id}/statistics', [DriverController::class, 'statistics']);
    Route::post('drivers/{id}/toggle-status', [DriverController::class, 'toggleStatus']);
    
    // Machines
    Route::apiResource('machines', MachineController::class);
    Route::get('machines/types/list', [MachineController::class, 'types']);
    Route::get('machines/{id}/statistics', [MachineController::class, 'statistics']);
    Route::post('machines/{id}/toggle-status', [MachineController::class, 'toggleStatus']);
    
    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('invoices/{id}/status', [InvoiceController::class, 'updateStatus']);
    Route::post('invoices/{id}/paid', [InvoiceController::class, 'markAsPaid']);
    Route::get('invoices/{id}/pdf', [InvoiceController::class, 'downloadPdf']);
    Route::post('invoices/{id}/email', [InvoiceController::class, 'sendEmail']);
    Route::post('jobs/{jobId}/invoice', [InvoiceController::class, 'generateFromJob']);
    Route::get('invoices-summary', [InvoiceController::class, 'summary']);
    
    // Payments
    Route::apiResource('payments', PaymentController::class);
    Route::get('payments-summary', [PaymentController::class, 'summary']);
    Route::get('customers/{customerId}/payments', [PaymentController::class, 'customerHistory']);
    
    // Expenses
    Route::apiResource('expenses', ExpenseController::class);
    Route::post('expenses/{id}/receipt', [ExpenseController::class, 'uploadReceipt']);
    Route::post('expenses/{id}/approve', [ExpenseController::class, 'approve']);
    Route::post('expenses/{id}/reject', [ExpenseController::class, 'reject']);
    Route::get('expenses-summary', [ExpenseController::class, 'summary']);
    Route::get('machines/{machineId}/expenses', [ExpenseController::class, 'machineExpenses']);
    Route::get('drivers/{driverId}/expenses', [ExpenseController::class, 'driverExpenses']);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('financial', [ReportController::class, 'financial']);
        Route::get('jobs', [ReportController::class, 'jobs']);
        Route::get('expenses', [ReportController::class, 'expenses']);
        Route::get('dashboard', [ReportController::class, 'dashboard']);
    });
    
    // Sync
    Route::post('sync/push', [SyncController::class, 'push']);
    Route::get('sync/pull', [SyncController::class, 'pull']);
});

// Health check endpoint (public)
Route::get('health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
    ]);
});
