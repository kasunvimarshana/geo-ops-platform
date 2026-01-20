<?php

namespace App\Repositories;

use App\Models\Land;
use App\Repositories\Contracts\LandRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Land Repository
 * 
 * Implements the LandRepositoryInterface.
 * Handles all database operations for lands.
 */
class LandRepository implements LandRepositoryInterface
{
    /**
     * Create a new land record
     * 
     * @param array $data
     * @return Land
     */
    public function create(array $data): Land
    {
        return Land::create($data);
    }

    /**
     * Update a land record
     * 
     * @param int $id
     * @param array $data
     * @return Land
     */
    public function update(int $id, array $data): Land
    {
        $land = $this->findById($id);
        $land->update($data);
        return $land->fresh();
    }

    /**
     * Delete a land record (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $land = $this->findById($id);
        return $land->delete();
    }

    /**
     * Find a land by ID
     * 
     * @param int $id
     * @return Land
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Land
    {
        return Land::with(['owner', 'organization'])
            ->findOrFail($id);
    }

    /**
     * Find all lands for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @return Collection
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection
    {
        $query = Land::where('organization_id', $organizationId)
            ->with(['owner']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['owner_user_id'])) {
            $query->where('owner_user_id', $filters['owner_user_id']);
        }

        if (isset($filters['location_district'])) {
            $query->where('location_district', $filters['location_district']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->get();
    }

    /**
     * Get paginated lands for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Land::where('organization_id', $organizationId)
            ->with(['owner']);

        // Apply same filters as findByOrganization
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['owner_user_id'])) {
            $query->where('owner_user_id', $filters['owner_user_id']);
        }

        if (isset($filters['location_district'])) {
            $query->where('location_district', $filters['location_district']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Find lands by owner user ID
     * 
     * @param int $userId
     * @return Collection
     */
    public function findByOwner(int $userId): Collection
    {
        return Land::where('owner_user_id', $userId)
            ->with(['organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find lands by status
     * 
     * @param int $organizationId
     * @param string $status
     * @return Collection
     */
    public function findByStatus(int $organizationId, string $status): Collection
    {
        return Land::where('organization_id', $organizationId)
            ->where('status', $status)
            ->with(['owner'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get lands with measurements count
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function withMeasurementsCount(int $organizationId): Collection
    {
        return Land::where('organization_id', $organizationId)
            ->withCount('measurements')
            ->with(['owner'])
            ->orderBy('measurements_count', 'desc')
            ->get();
    }

    /**
     * Get lands with field jobs count
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function withJobsCount(int $organizationId): Collection
    {
        return Land::where('organization_id', $organizationId)
            ->withCount('fieldJobs')
            ->with(['owner'])
            ->orderBy('field_jobs_count', 'desc')
            ->get();
    }
}
