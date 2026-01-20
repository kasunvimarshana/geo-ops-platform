<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobService
{
    /**
     * Create a new job
     */
    public function create(Organization $organization, array $data): Job
    {
        DB::beginTransaction();
        try {
            $job = Job::create([
                'organization_id' => $organization->id,
                'customer_id' => $data['customer_id'],
                'land_measurement_id' => $data['land_measurement_id'] ?? null,
                'driver_id' => $data['driver_id'] ?? null,
                'machine_id' => $data['machine_id'] ?? null,
                'status' => Job::STATUS_PENDING,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'],
            ]);

            DB::commit();
            return $job->load(['customer', 'landMeasurement', 'driver', 'machine']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create job', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update job status
     */
    public function updateStatus(Job $job, string $status): Job
    {
        $validStatuses = [
            Job::STATUS_PENDING,
            Job::STATUS_ASSIGNED,
            Job::STATUS_IN_PROGRESS,
            Job::STATUS_COMPLETED,
            Job::STATUS_BILLED,
            Job::STATUS_PAID
        ];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        DB::beginTransaction();
        try {
            $updates = ['status' => $status];

            // Auto-set timestamps based on status
            if ($status === Job::STATUS_IN_PROGRESS && !$job->started_at) {
                $updates['started_at'] = now();
            } elseif ($status === Job::STATUS_COMPLETED && !$job->completed_at) {
                $updates['completed_at'] = now();
            }

            $job->update($updates);

            DB::commit();
            return $job->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update job status', [
                'job_id' => $job->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Assign driver and machine to job
     */
    public function assign(Job $job, int $driverId, int $machineId): Job
    {
        DB::beginTransaction();
        try {
            $job->update([
                'driver_id' => $driverId,
                'machine_id' => $machineId,
                'status' => Job::STATUS_ASSIGNED,
            ]);

            DB::commit();
            return $job->load(['driver', 'machine']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign job', [
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
