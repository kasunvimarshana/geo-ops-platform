<?php

namespace App\Repositories;

use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class JobRepository implements JobRepositoryInterface
{
    public function __construct(
        protected Job $model
    ) {}

    public function findById(int $id): ?Job
    {
        return $this->model
            ->with(['customer', 'driver', 'machine', 'landMeasurement'])
            ->find($id);
    }

    public function findByOrganization(int $organizationId, array $filters = []): Collection
    {
        $query = $this->model
            ->where('organization_id', $organizationId)
            ->with(['customer', 'driver', 'machine', 'landMeasurement']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('scheduled_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('scheduled_date', '<=', $filters['date_to']);
        }

        return $query->latest()->get();
    }

    public function paginate(int $organizationId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->where('organization_id', $organizationId)
            ->with(['customer', 'driver', 'machine', 'landMeasurement']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('scheduled_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('scheduled_date', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Job
    {
        return $this->model->create($data);
    }

    public function update(Job $job, array $data): Job
    {
        $job->update($data);
        return $job->fresh(['customer', 'driver', 'machine', 'landMeasurement']);
    }

    public function delete(Job $job): bool
    {
        return $job->delete();
    }

    public function findByStatus(int $organizationId, string $status): Collection
    {
        return $this->model
            ->where('organization_id', $organizationId)
            ->where('status', $status)
            ->with(['customer', 'driver', 'machine', 'landMeasurement'])
            ->latest()
            ->get();
    }

    public function findByDriver(int $driverId): Collection
    {
        return $this->model
            ->where('driver_id', $driverId)
            ->with(['customer', 'machine', 'landMeasurement'])
            ->latest()
            ->get();
    }

    public function findByCustomer(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->with(['driver', 'machine', 'landMeasurement'])
            ->latest()
            ->get();
    }

    public function updateStatus(Job $job, string $status): Job
    {
        $job->update(['status' => $status]);
        return $job->fresh(['customer', 'driver', 'machine', 'landMeasurement']);
    }

    public function countByStatus(int $organizationId, string $status): int
    {
        return $this->model
            ->where('organization_id', $organizationId)
            ->where('status', $status)
            ->count();
    }
}
