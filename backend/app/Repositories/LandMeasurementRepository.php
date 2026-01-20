<?php

namespace App\Repositories;

use App\Models\LandMeasurement;
use App\Repositories\Contracts\LandMeasurementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LandMeasurementRepository implements LandMeasurementRepositoryInterface
{
    public function __construct(
        protected LandMeasurement $model
    ) {}

    public function findById(int $id): ?LandMeasurement
    {
        return $this->model->with(['customer'])->find($id);
    }

    public function findByOrganization(int $organizationId): Collection
    {
        return $this->model
            ->where('organization_id', $organizationId)
            ->with(['customer'])
            ->latest()
            ->get();
    }

    public function paginate(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('organization_id', $organizationId)
            ->with(['customer'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): LandMeasurement
    {
        return $this->model->create($data);
    }

    public function update(LandMeasurement $measurement, array $data): LandMeasurement
    {
        $measurement->update($data);
        return $measurement->fresh(['customer']);
    }

    public function delete(LandMeasurement $measurement): bool
    {
        return $measurement->delete();
    }

    public function findByCustomer(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    public function findByAreaRange(int $organizationId, float $minAcres, float $maxAcres): Collection
    {
        return $this->model
            ->where('organization_id', $organizationId)
            ->whereBetween('area_acres', [$minAcres, $maxAcres])
            ->with(['customer'])
            ->latest()
            ->get();
    }
}
