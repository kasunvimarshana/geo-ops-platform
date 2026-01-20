<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Repositories\FieldJobRepositoryInterface;
use App\Models\FieldJob;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobService
{
    public function __construct(
        private readonly FieldJobRepositoryInterface $repository
    ) {}

    public function getById(int $id): ?FieldJob
    {
        return $this->repository->findById($id);
    }

    public function getAllByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateByOrganization($organizationId, $perPage);
    }

    public function getByStatus(int $organizationId, string $status): array
    {
        return $this->repository->findByStatus($organizationId, $status)->toArray();
    }

    public function create(array $data): FieldJob
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): FieldJob
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function assignDriver(int $jobId, int $driverId): FieldJob
    {
        return $this->repository->update($jobId, [
            'driver_id' => $driverId,
            'status' => 'assigned',
        ]);
    }

    public function startJob(int $jobId): FieldJob
    {
        return $this->repository->update($jobId, [
            'status' => 'in_progress',
            'start_time' => now(),
        ]);
    }

    public function completeJob(int $jobId): FieldJob
    {
        $job = $this->repository->findById($jobId);
        
        $endTime = now();
        $durationHours = null;
        
        if ($job->start_time) {
            $durationHours = $endTime->diffInMinutes($job->start_time) / 60;
        }

        return $this->repository->update($jobId, [
            'status' => 'completed',
            'end_time' => $endTime,
            'duration_hours' => $durationHours,
        ]);
    }

    public function cancelJob(int $jobId): FieldJob
    {
        return $this->repository->updateStatus($jobId, 'cancelled');
    }
}
