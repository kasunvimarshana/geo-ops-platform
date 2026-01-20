<?php

namespace App\Repositories;

use App\Models\Measurement;
use App\Repositories\Contracts\MeasurementRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Measurement Repository
 * 
 * Handles all database operations for measurements.
 */
class MeasurementRepository implements MeasurementRepositoryInterface
{
    public function create(array $data): Measurement
    {
        return Measurement::create($data);
    }

    public function update(int $id, array $data): Measurement
    {
        $measurement = $this->findById($id);
        $measurement->update($data);
        return $measurement->fresh();
    }

    public function delete(int $id): bool
    {
        $measurement = $this->findById($id);
        return $measurement->delete();
    }

    public function findById(int $id): Measurement
    {
        return Measurement::with(['land', 'user', 'organization'])
            ->findOrFail($id);
    }

    public function findByOrganization(int $organizationId, array $filters = []): Collection
    {
        $query = Measurement::where('organization_id', $organizationId)
            ->with(['land', 'user']);

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Measurement::where('organization_id', $organizationId)
            ->with(['land', 'user']);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function findByLand(int $landId): Collection
    {
        return Measurement::where('land_id', $landId)
            ->with(['user'])
            ->orderBy('measurement_started_at', 'desc')
            ->get();
    }

    public function findByUser(int $userId): Collection
    {
        return Measurement::where('user_id', $userId)
            ->with(['land'])
            ->orderBy('measurement_started_at', 'desc')
            ->get();
    }

    public function findUnsynced(int $organizationId): Collection
    {
        return Measurement::where('organization_id', $organizationId)
            ->where('is_synced', false)
            ->with(['land', 'user'])
            ->orderBy('measurement_started_at', 'desc')
            ->get();
    }

    public function createBatch(array $measurements): Collection
    {
        $created = collect();
        
        foreach ($measurements as $measurementData) {
            $created->push($this->create($measurementData));
        }
        
        return $created;
    }

    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['land_id'])) {
            $query->where('land_id', $filters['land_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_synced'])) {
            $query->where('is_synced', $filters['is_synced']);
        }

        if (isset($filters['start_date'])) {
            $query->where('measurement_started_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('measurement_completed_at', '<=', $filters['end_date']);
        }

        $sortBy = $filters['sort_by'] ?? 'measurement_started_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);
    }
}
