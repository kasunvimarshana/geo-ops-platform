<?php

namespace App\Services;

use App\Repositories\Contracts\FieldJobRepositoryInterface;
use App\Repositories\Contracts\LandRepositoryInterface;
use App\DTOs\FieldJob\CreateJobDTO;
use App\DTOs\FieldJob\UpdateJobDTO;
use App\DTOs\FieldJob\AssignJobDTO;
use App\DTOs\FieldJob\CompleteJobDTO;
use App\Models\FieldJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * FieldJob Service
 * 
 * Handles all business logic related to field job management.
 * Manages job lifecycle: pending → assigned → in_progress → completed
 */
class FieldJobService
{
    public function __construct(
        private FieldJobRepositoryInterface $jobRepository,
        private LandRepositoryInterface $landRepository
    ) {}

    /**
     * Create a new field job
     */
    public function createJob(CreateJobDTO $dto): FieldJob
    {
        return DB::transaction(function () use ($dto) {
            $user = Auth::user();
            
            // Validate related entities belong to same organization
            if ($dto->landId) {
                $land = $this->landRepository->findById($dto->landId);
                if ($land->organization_id !== $user->organization_id) {
                    throw new \Exception('Land does not belong to your organization');
                }
            }

            if ($dto->customerId) {
                $customer = User::findOrFail($dto->customerId);
                if ($customer->organization_id !== $user->organization_id) {
                    throw new \Exception('Customer does not belong to your organization');
                }
            }

            // Generate unique job number
            $jobNumber = $this->jobRepository->generateJobNumber($user->organization_id);
            
            // Prepare job data
            $jobData = [
                'organization_id' => $user->organization_id,
                'job_number' => $jobNumber,
                'status' => 'pending',
                'land_id' => $dto->landId,
                'customer_id' => $dto->customerId,
                'service_type' => $dto->serviceType,
                'customer_name' => $dto->customerName,
                'customer_phone' => $dto->customerPhone,
                'customer_address' => $dto->customerAddress,
                'location_coordinates' => $dto->locationCoordinates,
                'area_acres' => $dto->areaAcres,
                'area_hectares' => $dto->areaHectares,
                'rate_per_unit' => $dto->ratePerUnit,
                'rate_unit' => $dto->rateUnit,
                'estimated_amount' => $dto->estimatedAmount,
                'scheduled_date' => $dto->scheduledDate,
                'notes' => $dto->notes,
                'is_synced' => false,
                'created_by' => $user->id,
            ];
            
            $job = $this->jobRepository->create($jobData);
            
            return $job;
        });
    }

    /**
     * Update an existing field job
     */
    public function updateJob(int $jobId, UpdateJobDTO $dto): FieldJob
    {
        return DB::transaction(function () use ($jobId, $dto) {
            $user = Auth::user();
            
            $job = $this->jobRepository->findById($jobId);
            
            // Ensure job belongs to user's organization
            if ($job->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to job');
            }

            // Validate related entities if being updated
            if ($dto->landId) {
                $land = $this->landRepository->findById($dto->landId);
                if ($land->organization_id !== $user->organization_id) {
                    throw new \Exception('Land does not belong to your organization');
                }
            }

            if ($dto->customerId) {
                $customer = User::findOrFail($dto->customerId);
                if ($customer->organization_id !== $user->organization_id) {
                    throw new \Exception('Customer does not belong to your organization');
                }
            }

            // Validate status transition if status is being changed
            if ($dto->status && $dto->status !== $job->status) {
                $this->validateStatusTransition($job->status, $dto->status);
            }
            
            $updateData = $dto->toArray();
            $updateData['updated_by'] = $user->id;
            $updateData['is_synced'] = false;
            
            $job = $this->jobRepository->update($job->id, $updateData);
            
            return $job;
        });
    }

    /**
     * Assign job to driver
     */
    public function assignJob(int $jobId, AssignJobDTO $dto): FieldJob
    {
        return DB::transaction(function () use ($jobId, $dto) {
            $user = Auth::user();
            
            $job = $this->jobRepository->findById($jobId);
            
            // Ensure job belongs to user's organization
            if ($job->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to job');
            }

            // Validate current status
            if (!in_array($job->status, ['pending', 'assigned'])) {
                throw new \Exception('Job can only be assigned when status is pending or already assigned');
            }

            // Validate driver exists and belongs to same organization
            $driver = User::findOrFail($dto->driverId);
            if ($driver->organization_id !== $user->organization_id) {
                throw new \Exception('Driver does not belong to your organization');
            }

            // Update job
            $updateData = [
                'driver_id' => $dto->driverId,
                'status' => 'assigned',
                'updated_by' => $user->id,
                'is_synced' => false,
            ];

            if ($dto->notes) {
                $updateData['notes'] = ($job->notes ? $job->notes . "\n" : '') . 
                    "[Assignment] " . $dto->notes;
            }

            $job = $this->jobRepository->update($job->id, $updateData);
            
            return $job;
        });
    }

    /**
     * Start job (change to in_progress)
     */
    public function startJob(int $jobId): FieldJob
    {
        return DB::transaction(function () use ($jobId) {
            $user = Auth::user();
            
            $job = $this->jobRepository->findById($jobId);
            
            // Ensure job belongs to user's organization
            if ($job->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to job');
            }

            // Validate current status
            if (!in_array($job->status, ['assigned', 'pending'])) {
                throw new \Exception('Job must be assigned or pending to start');
            }

            // Update job
            $updateData = [
                'status' => 'in_progress',
                'started_at' => Carbon::now(),
                'updated_by' => $user->id,
                'is_synced' => false,
            ];

            $job = $this->jobRepository->update($job->id, $updateData);
            
            return $job;
        });
    }

    /**
     * Complete job
     */
    public function completeJob(int $jobId, CompleteJobDTO $dto): FieldJob
    {
        return DB::transaction(function () use ($jobId, $dto) {
            $user = Auth::user();
            
            $job = $this->jobRepository->findById($jobId);
            
            // Ensure job belongs to user's organization
            if ($job->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to job');
            }

            // Validate current status
            if (!in_array($job->status, ['in_progress', 'assigned'])) {
                throw new \Exception('Job must be in progress or assigned to complete');
            }

            $completedAt = Carbon::now();
            
            // Calculate duration if job was started
            $durationMinutes = null;
            if ($job->started_at) {
                $durationMinutes = $job->started_at->diffInMinutes($completedAt);
            }

            // Update job
            $updateData = [
                'status' => 'completed',
                'completed_at' => $completedAt,
                'duration_minutes' => $durationMinutes,
                'actual_amount' => $dto->actualAmount,
                'distance_km' => $dto->distanceKm,
                'completion_notes' => $dto->completionNotes,
                'attachments' => $dto->attachments,
                'updated_by' => $user->id,
                'is_synced' => false,
            ];

            // Set started_at if not already set
            if (!$job->started_at) {
                $updateData['started_at'] = $completedAt;
            }

            $job = $this->jobRepository->update($job->id, $updateData);
            
            return $job;
        });
    }

    /**
     * Get all jobs for the current organization
     */
    public function getJobs(array $filters = [])
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->jobRepository->findByOrganization($organizationId, $filters);
    }

    /**
     * Get a specific job by ID
     */
    public function getJob(int $jobId): FieldJob
    {
        $user = Auth::user();
        $job = $this->jobRepository->findById($jobId);
        
        // Ensure job belongs to user's organization
        if ($job->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to job');
        }
        
        return $job;
    }

    /**
     * Delete a job (soft delete)
     */
    public function deleteJob(int $jobId): bool
    {
        $user = Auth::user();
        $job = $this->jobRepository->findById($jobId);
        
        // Ensure job belongs to user's organization
        if ($job->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to job');
        }
        
        // Prevent deletion of completed jobs
        if ($job->status === 'completed') {
            throw new \Exception('Cannot delete completed jobs');
        }
        
        $result = $this->jobRepository->delete($job->id);
        
        return $result;
    }

    /**
     * Get jobs with pagination
     */
    public function getJobsPaginated(array $filters = [], int $perPage = 15)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->jobRepository->paginateByOrganization($organizationId, $filters, $perPage);
    }

    /**
     * Validate status transitions
     */
    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $validTransitions = [
            'pending' => ['assigned', 'cancelled'],
            'assigned' => ['in_progress', 'cancelled', 'pending'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => ['pending'],
        ];

        if (!isset($validTransitions[$currentStatus])) {
            throw new \Exception('Invalid current status');
        }

        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            throw new \Exception("Cannot transition from {$currentStatus} to {$newStatus}");
        }
    }
}
