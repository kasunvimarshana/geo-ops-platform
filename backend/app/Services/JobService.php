<?php

namespace App\Services;

use App\Models\Job;
use App\Repositories\JobRepository;
use Illuminate\Support\Facades\DB;

class JobService
{
    protected $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function createJob(array $data): Job
    {
        DB::beginTransaction();
        try {
            $job = $this->jobRepository->create($data);
            DB::commit();
            return $job;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateJob(int $id, array $data): Job
    {
        DB::beginTransaction();
        try {
            $job = $this->jobRepository->update($id, $data);
            DB::commit();
            return $job;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getJobById(int $id): ?Job
    {
        return $this->jobRepository->find($id);
    }

    public function getAllJobs(array $filters = []): array
    {
        return $this->jobRepository->getAll($filters);
    }

    public function deleteJob(int $id): bool
    {
        return $this->jobRepository->delete($id);
    }
}