<?php

namespace App\Repositories\Contracts;

use App\Models\Land;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Land Repository Interface
 * 
 * Defines the contract for land data access operations.
 * Implementations must provide these methods.
 */
interface LandRepositoryInterface
{
    /**
     * Create a new land record
     * 
     * @param array $data
     * @return Land
     */
    public function create(array $data): Land;

    /**
     * Update a land record
     * 
     * @param int $id
     * @param array $data
     * @return Land
     */
    public function update(int $id, array $data): Land;

    /**
     * Delete a land record (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find a land by ID
     * 
     * @param int $id
     * @return Land
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Land;

    /**
     * Find all lands for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @return Collection
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection;

    /**
     * Get paginated lands for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find lands by owner user ID
     * 
     * @param int $userId
     * @return Collection
     */
    public function findByOwner(int $userId): Collection;

    /**
     * Find lands by status
     * 
     * @param int $organizationId
     * @param string $status
     * @return Collection
     */
    public function findByStatus(int $organizationId, string $status): Collection;

    /**
     * Get lands with measurements count
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function withMeasurementsCount(int $organizationId): Collection;

    /**
     * Get lands with jobs count
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function withJobsCount(int $organizationId): Collection;
}
