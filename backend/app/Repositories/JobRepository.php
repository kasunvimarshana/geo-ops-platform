<?php

namespace App\Repositories;

use App\Models\Job;
use App\Repositories\Interfaces\JobRepositoryInterface;

class JobRepository implements JobRepositoryInterface
{
    public function create(array $data): object
    {
        return Job::create($data);
    }

    public function findById(int $id): ?object
    {
        return Job::with(['land', 'machine', 'driver', 'tracking', 'expenses', 'invoice'])->find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?object
    {
        return Job::with(['land', 'machine', 'driver', 'tracking', 'expenses'])
            ->where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function findByOrganization(int $organizationId, array $filters = []): object
    {
        $query = Job::with(['land', 'machine', 'driver'])
            ->where('organization_id', $organizationId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (isset($filters['machine_id'])) {
            $query->where('machine_id', $filters['machine_id']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('customer_name', 'like', "%{$filters['search']}%")
                    ->orWhere('location_name', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('job_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('job_date', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('job_date', 'desc')->paginate($perPage);
    }

    public function update(int $id, array $data): bool
    {
        $job = Job::find($id);
        return $job ? $job->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $job = Job::find($id);
        return $job ? $job->delete() : false;
    }

    public function findByOfflineId(string $offlineId, int $organizationId): ?object
    {
        return Job::where('offline_id', $offlineId)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function getPendingSync(int $organizationId): array
    {
        return Job::where('organization_id', $organizationId)
            ->where('sync_status', 'pending')
            ->get()
            ->toArray();
    }

    public function findActiveJobs(int $organizationId): array
    {
        return Job::with(['land', 'machine', 'driver'])
            ->where('organization_id', $organizationId)
            ->whereIn('status', ['scheduled', 'in-progress'])
            ->orderBy('job_date', 'asc')
            ->get()
            ->toArray();
    }

    public function findByDriver(int $driverId, array $filters = []): object
    {
        $query = Job::with(['land', 'machine'])
            ->where('driver_id', $driverId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('job_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('job_date', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('job_date', 'desc')->paginate($perPage);
    }
}
