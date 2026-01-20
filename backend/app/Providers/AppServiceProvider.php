<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository Bindings
        $this->app->bind(
            \App\Repositories\Contracts\JobRepositoryInterface::class,
            \App\Repositories\JobRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\LandMeasurementRepositoryInterface::class,
            \App\Repositories\LandMeasurementRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\InvoiceRepositoryInterface::class,
            \App\Repositories\InvoiceRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
