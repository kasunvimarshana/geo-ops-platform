<?php

use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
    Route::get('me', [App\Http\Controllers\Api\V1\AuthController::class, 'me']);
});

// Customer Routes
Route::apiResource('customers', App\Http\Controllers\Api\V1\CustomerController::class);

// Land Routes
Route::apiResource('lands', App\Http\Controllers\Api\V1\LandController::class);

// Job Routes
Route::apiResource('jobs', App\Http\Controllers\Api\V1\JobController::class);
Route::post('jobs/{id}/start', [App\Http\Controllers\Api\V1\JobController::class, 'start']);
Route::post('jobs/{id}/complete', [App\Http\Controllers\Api\V1\JobController::class, 'complete']);
Route::post('jobs/{id}/tracking', [App\Http\Controllers\Api\V1\JobController::class, 'tracking']);

// Invoice Routes
Route::apiResource('invoices', App\Http\Controllers\Api\V1\InvoiceController::class);
Route::get('invoices/{id}/pdf', [App\Http\Controllers\Api\V1\InvoiceController::class, 'generatePDF']);

// Payment Routes
Route::apiResource('payments', App\Http\Controllers\Api\V1\PaymentController::class);

// Expense Routes
Route::apiResource('expenses', App\Http\Controllers\Api\V1\ExpenseController::class);

// Driver Routes
Route::apiResource('drivers', App\Http\Controllers\Api\V1\DriverController::class);

// Organization Routes
Route::apiResource('organizations', App\Http\Controllers\Api\V1\OrganizationController::class);

// Subscription Routes
Route::apiResource('subscriptions', App\Http\Controllers\Api\V1\SubscriptionController::class);

// Dashboard Routes
Route::get('dashboard', [App\Http\Controllers\Api\V1\DashboardController::class, 'index']);