<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Repositories\JobRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $jobs = $this->jobRepository->getAllJobs($request->user()->id);
        return response()->json(JobResource::collection($jobs));
    }

    public function store(CreateJobRequest $request): JsonResponse
    {
        $job = $this->jobRepository->createJob($request->validated());
        return response()->json(new JobResource($job), 201);
    }

    public function show($id): JsonResponse
    {
        $job = $this->jobRepository->findJobById($id);
        return response()->json(new JobResource($job));
    }

    public function update(CreateJobRequest $request, $id): JsonResponse
    {
        $job = $this->jobRepository->updateJob($id, $request->validated());
        return response()->json(new JobResource($job));
    }

    public function destroy($id): JsonResponse
    {
        $this->jobRepository->deleteJob($id);
        return response()->json(null, 204);
    }
}