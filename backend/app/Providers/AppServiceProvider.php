<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\LandRepositoryInterface;
use App\Repositories\LandRepository;
use App\Repositories\Contracts\MeasurementRepositoryInterface;
use App\Repositories\MeasurementRepository;
use App\Repositories\Contracts\FieldJobRepositoryInterface;
use App\Repositories\FieldJobRepository;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\InvoiceRepository;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\PaymentRepository;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\ExpenseRepository;
use App\Repositories\Contracts\TrackingRepositoryInterface;
use App\Repositories\TrackingRepository;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\SubscriptionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LandRepositoryInterface::class, LandRepository::class);
        $this->app->bind(MeasurementRepositoryInterface::class, MeasurementRepository::class);
        $this->app->bind(FieldJobRepositoryInterface::class, FieldJobRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(TrackingRepositoryInterface::class, TrackingRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
