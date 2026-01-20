<?php

namespace App\Repositories\Contracts;

use App\Models\TrackingLog;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Tracking Repository Interface
 * 
 * Defines the contract for tracking log data access operations.
 */
interface TrackingRepositoryInterface
{
    /**
     * Create a new tracking log record
     */
    public function create(array $data): TrackingLog;

    /**
     * Batch create tracking log records
     */
    public function batchCreate(array $logsData): int;

    /**
     * Find a tracking log by ID
     */
    public function findById(int $id): TrackingLog;

    /**
     * Find all tracking logs for a specific user
     */
    public function findByUser(int $userId, array $filters = []): Collection;

    /**
     * Get paginated tracking logs for a specific user
     */
    public function paginateByUser(int $userId, array $filters = [], int $perPage = 50): LengthAwarePaginator;

    /**
     * Find tracking logs by job
     */
    public function findByJob(int $jobId, array $filters = []): Collection;

    /**
     * Get paginated tracking logs for a specific job
     */
    public function paginateByJob(int $jobId, array $filters = [], int $perPage = 50): LengthAwarePaginator;

    /**
     * Get tracking logs within date range
     */
    public function findByDateRange(int $organizationId, string $startDate, string $endDate): Collection;

    /**
     * Get live locations (last update within 5 minutes)
     */
    public function getLiveLocations(int $organizationId): Collection;

    /**
     * Get last tracking log for a user
     */
    public function getLastLocation(int $userId): ?TrackingLog;

    /**
     * Calculate distance traveled for a user/job
     */
    public function calculateDistance(int $userId, ?int $jobId = null, ?string $startDate = null, ?string $endDate = null): float;
}
