<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\LandRepositoryInterface;
use App\Repositories\LandRepository;
use App\Repositories\Interfaces\JobRepositoryInterface;
use App\Repositories\JobRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\InvoiceRepository;
use App\Repositories\Interfaces\ExpenseRepositoryInterface;
use App\Repositories\ExpenseRepository;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\PaymentRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\MachineRepositoryInterface;
use App\Repositories\MachineRepository;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Repositories\OrganizationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LandRepositoryInterface::class, LandRepository::class);
        $this->app->bind(JobRepositoryInterface::class, JobRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(MachineRepositoryInterface::class, MachineRepository::class);
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
