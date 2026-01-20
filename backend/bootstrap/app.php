<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

require __DIR__.'/../vendor/autoload.php';

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// Load the application configuration
$app->make('config')->set('app', require_once __DIR__.'/../config/app.php');

// Load the application routes
Route::middleware('api')->group(function () {
    require __DIR__.'/../routes/api.php';
});

// Run the application
$app->run();