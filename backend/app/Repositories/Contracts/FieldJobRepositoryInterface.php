<?php

namespace App\Repositories\Contracts;

use App\Models\FieldJob;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * FieldJob Repository Interface
 * 
 * Defines the contract for field job data access operations.
 */
interface FieldJobRepositoryInterface
{
    /**
     * Create a new field job record
     * 
     * @param array $data
     * @return FieldJob
     */
    public function create(array $data): FieldJob;

    /**
     * Update a field job record
     * 
     * @param int $id
     * @param array $data
     * @return FieldJob
     */
    public function update(int $id, array $data): FieldJob;

    /**
     * Delete a field job (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find a field job by ID
     * 
     * @param int $id
     * @return FieldJob
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): FieldJob;

    /**
     * Find all jobs for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @return Collection
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection;

    /**
     * Get paginated jobs for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find jobs by status
     * 
     * @param int $organizationId
     * @param string $status
     * @return Collection
     */
    public function findByStatus(int $organizationId, string $status): Collection;

    /**
     * Find jobs by driver
     * 
     * @param int $driverId
     * @return Collection
     */
    public function findByDriver(int $driverId): Collection;

    /**
     * Find jobs by customer
     * 
     * @param int $customerId
     * @return Collection
     */
    public function findByCustomer(int $customerId): Collection;

    /**
     * Get jobs scheduled between dates
     * 
     * @param int $organizationId
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function findScheduledBetween(int $organizationId, string $startDate, string $endDate): Collection;

    /**
     * Generate unique job number
     * 
     * @param int $organizationId
     * @return string
     */
    public function generateJobNumber(int $organizationId): string;
}
