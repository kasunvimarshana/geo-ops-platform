<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Repositories\FieldJobRepositoryInterface;
use App\Domain\Repositories\InvoiceRepositoryInterface;
use App\Domain\Repositories\LandPlotRepositoryInterface;
use App\Domain\Repositories\OrganizationRepositoryInterface;
use App\Infrastructure\Repositories\FieldJobRepository;
use App\Infrastructure\Repositories\InvoiceRepository;
use App\Infrastructure\Repositories\LandPlotRepository;
use App\Infrastructure\Repositories\OrganizationRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
        $this->app->bind(LandPlotRepositoryInterface::class, LandPlotRepository::class);
        $this->app->bind(FieldJobRepositoryInterface::class, FieldJobRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
