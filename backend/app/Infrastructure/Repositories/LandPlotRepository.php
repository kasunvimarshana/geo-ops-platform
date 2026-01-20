<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\LandPlotRepositoryInterface;
use App\Models\LandPlot;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LandPlotRepository implements LandPlotRepositoryInterface
{
    public function findById(int $id): ?LandPlot
    {
        return LandPlot::with(['organization', 'user'])->find($id);
    }

    public function findByOrganization(int $organizationId): Collection
    {
        return LandPlot::organization($organizationId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function paginateByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return LandPlot::organization($organizationId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): LandPlot
    {
        return LandPlot::create($data);
    }

    public function update(int $id, array $data): LandPlot
    {
        $landPlot = LandPlot::findOrFail($id);
        $landPlot->update($data);
        return $landPlot->fresh(['organization', 'user']);
    }

    public function delete(int $id): bool
    {
        $landPlot = LandPlot::findOrFail($id);
        return $landPlot->delete();
    }
}
