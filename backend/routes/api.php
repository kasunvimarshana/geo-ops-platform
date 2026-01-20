<?php

declare(strict_types=1);

use App\Presentation\Controllers\Api\AuthController;
use App\Presentation\Controllers\Api\FieldJobController;
use App\Presentation\Controllers\Api\InvoiceController;
use App\Presentation\Controllers\Api\LandPlotController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Land Plots
    Route::apiResource('land-plots', LandPlotController::class);

    // Field Jobs
    Route::prefix('field-jobs')->group(function () {
        Route::post('{id}/start', [FieldJobController::class, 'start']);
        Route::post('{id}/complete', [FieldJobController::class, 'complete']);
        Route::post('{id}/cancel', [FieldJobController::class, 'cancel']);
    });
    Route::apiResource('field-jobs', FieldJobController::class);

    // Invoices
    Route::prefix('invoices')->group(function () {
        Route::get('{id}/generate-pdf', [InvoiceController::class, 'generatePdf']);
        Route::get('{id}/download-pdf', [InvoiceController::class, 'downloadPdf']);
    });
    Route::apiResource('invoices', InvoiceController::class);
});
