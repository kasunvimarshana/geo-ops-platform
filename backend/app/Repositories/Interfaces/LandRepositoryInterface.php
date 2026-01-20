<?php

namespace App\Repositories\Interfaces;

/**
 * Land Repository Interface
 * 
 * Defines contract for land data access
 * Following Repository pattern for Clean Architecture
 */
interface LandRepositoryInterface
{
    /**
     * Create a new land record
     */
    public function create(array $data): object;

    /**
     * Find land by ID
     */
    public function findById(int $id): ?object;

    /**
     * Find land by ID and organization
     */
    public function findByIdAndOrganization(int $id, int $organizationId): ?object;

    /**
     * Find all lands for organization
     */
    public function findByOrganization(int $organizationId, array $filters = []): object;

    /**
     * Find nearby lands using spatial query
     */
    public function findNearby(float $latitude, float $longitude, int $radiusMeters, int $organizationId): array;

    /**
     * Update land record
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete land record (soft delete)
     */
    public function delete(int $id): bool;

    /**
     * Find by offline ID for sync
     */
    public function findByOfflineId(string $offlineId, int $organizationId): ?object;

    /**
     * Get pending sync records
     */
    public function getPendingSync(int $organizationId): array;
}
