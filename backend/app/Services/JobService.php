<?php

namespace App\Services;

use App\Repositories\Interfaces\JobRepositoryInterface;
use App\Repositories\Interfaces\LandRepositoryInterface;
use App\Models\JobTracking;
use Illuminate\Support\Facades\DB;

class JobService
{
    public function __construct(
        private JobRepositoryInterface $jobRepository,
        private LandRepositoryInterface $landRepository
    ) {}

    public function createJob(array $data, int $organizationId, int $userId): object
    {
        DB::beginTransaction();
        
        try {
            $jobData = array_merge($data, [
                'organization_id' => $organizationId,
                'assigned_by' => $userId,
                'status' => 'scheduled',
                'sync_status' => 'synced',
            ]);

            $job = $this->jobRepository->create($jobData);

            DB::commit();
            return $job;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateJob(int $id, array $data, int $organizationId): object
    {
        $job = $this->jobRepository->findByIdAndOrganization($id, $organizationId);
        
        if (!$job) {
            throw new \Exception('Job not found');
        }

        $this->jobRepository->update($id, $data);
        
        return $this->jobRepository->findById($id);
    }

    public function updateStatus(int $id, string $status, int $organizationId, ?array $location = null): object
    {
        $job = $this->jobRepository->findByIdAndOrganization($id, $organizationId);
        
        if (!$job) {
            throw new \Exception('Job not found');
        }

        DB::beginTransaction();
        
        try {
            $updateData = ['status' => $status];

            if ($status === 'in-progress' && !$job->start_time) {
                $updateData['start_time'] = now();
            }

            if ($status === 'completed' && !$job->end_time) {
                $updateData['end_time'] = now();
                
                if ($job->start_time) {
                    $duration = now()->diffInMinutes($job->start_time);
                    $updateData['duration_minutes'] = $duration;
                }
            }

            $this->jobRepository->update($id, $updateData);

            if ($location) {
                JobTracking::create([
                    'job_id' => $id,
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                    'accuracy' => $location['accuracy'] ?? null,
                    'status' => $status,
                    'notes' => $location['notes'] ?? null,
                    'recorded_at' => now(),
                ]);
            }

            DB::commit();
            
            return $this->jobRepository->findById($id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function trackLocation(int $jobId, array $location, int $organizationId): void
    {
        $job = $this->jobRepository->findByIdAndOrganization($jobId, $organizationId);
        
        if (!$job) {
            throw new \Exception('Job not found');
        }

        JobTracking::create([
            'job_id' => $jobId,
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'accuracy' => $location['accuracy'] ?? null,
            'status' => $job->status,
            'notes' => $location['notes'] ?? null,
            'recorded_at' => $location['recorded_at'] ?? now(),
        ]);
    }

    public function getActiveJobs(int $organizationId): array
    {
        return $this->jobRepository->findActiveJobs($organizationId);
    }

    public function deleteJob(int $id, int $organizationId): bool
    {
        $job = $this->jobRepository->findByIdAndOrganization($id, $organizationId);
        
        if (!$job) {
            throw new \Exception('Job not found');
        }

        return $this->jobRepository->delete($id);
    }
}
