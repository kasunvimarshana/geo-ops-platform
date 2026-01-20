<?php

use Illuminate\Support\Facades\Route;

// Web routes for the application
Route::get('/', function () {
    return view('welcome');
});

// Additional web routes can be defined here as needed.