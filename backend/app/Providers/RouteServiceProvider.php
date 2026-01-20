<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers\Api\V1';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        Route::prefix('api/v1')
            ->namespace($this->namespace)
            ->group(base_path('backend/routes/api.php'));
    }
}