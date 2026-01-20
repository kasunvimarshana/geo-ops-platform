<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\LandPlot;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LandPlotRepositoryInterface
{
    public function findById(int $id): ?LandPlot;

    public function findByOrganization(int $organizationId): Collection;

    public function paginateByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): LandPlot;

    public function update(int $id, array $data): LandPlot;

    public function delete(int $id): bool;
}
