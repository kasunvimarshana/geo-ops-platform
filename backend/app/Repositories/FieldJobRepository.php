<?php

namespace App\Repositories;

use App\Models\FieldJob;
use App\Repositories\Contracts\FieldJobRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * FieldJob Repository
 * 
 * Implements the FieldJobRepositoryInterface.
 * Handles all database operations for field jobs.
 */
class FieldJobRepository implements FieldJobRepositoryInterface
{
    /**
     * Create a new field job record
     */
    public function create(array $data): FieldJob
    {
        return FieldJob::create($data);
    }

    /**
     * Update a field job record
     */
    public function update(int $id, array $data): FieldJob
    {
        $job = $this->findById($id);
        $job->update($data);
        return $job->fresh();
    }

    /**
     * Delete a field job (soft delete)
     */
    public function delete(int $id): bool
    {
        $job = $this->findById($id);
        return $job->delete();
    }

    /**
     * Find a field job by ID
     */
    public function findById(int $id): FieldJob
    {
        return FieldJob::with(['land', 'customer', 'driver', 'organization'])
            ->findOrFail($id);
    }

    /**
     * Find all jobs for a specific organization
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection
    {
        $query = FieldJob::where('organization_id', $organizationId)
            ->with(['land', 'customer', 'driver']);

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated jobs for a specific organization
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = FieldJob::where('organization_id', $organizationId)
            ->with(['land', 'customer', 'driver']);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Find jobs by status
     */
    public function findByStatus(int $organizationId, string $status): Collection
    {
        return FieldJob::where('organization_id', $organizationId)
            ->where('status', $status)
            ->with(['land', 'customer', 'driver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find jobs by driver
     */
    public function findByDriver(int $driverId): Collection
    {
        return FieldJob::where('driver_id', $driverId)
            ->with(['land', 'customer', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find jobs by customer
     */
    public function findByCustomer(int $customerId): Collection
    {
        return FieldJob::where('customer_id', $customerId)
            ->with(['land', 'driver', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get jobs scheduled between dates
     */
    public function findScheduledBetween(int $organizationId, string $startDate, string $endDate): Collection
    {
        return FieldJob::where('organization_id', $organizationId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with(['land', 'customer', 'driver'])
            ->orderBy('scheduled_date', 'asc')
            ->get();
    }

    /**
     * Generate unique job number
     */
    public function generateJobNumber(int $organizationId): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "JOB-{$date}-";
        
        // Get the last job number for today
        $lastJob = FieldJob::where('organization_id', $organizationId)
            ->where('job_number', 'like', "{$prefix}%")
            ->orderBy('job_number', 'desc')
            ->first();

        if ($lastJob) {
            // Extract the sequence number and increment
            $lastSequence = intval(substr($lastJob->job_number, -4));
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        if (isset($filters['land_id'])) {
            $query->where('land_id', $filters['land_id']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('scheduled_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);
    }
}
