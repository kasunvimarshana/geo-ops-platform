<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api;

use App\Application\Services\JobService;
use App\Http\Controllers\Controller;
use App\Presentation\Resources\FieldJobResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class FieldJobController extends Controller
{
    public function __construct(
        private readonly JobService $jobService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $organizationId = auth()->user()->organization_id;
        $jobs = $this->jobService->getAllByOrganization($organizationId, $request->get('per_page', 15));
        return FieldJobResource::collection($jobs);
    }

    public function show(int $id): JsonResponse
    {
        $job = $this->jobService->getById($id);
        
        if (!$job || $job->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        return response()->json(['data' => new FieldJobResource($job)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'job_type' => 'required|in:plowing,harvesting,spraying,seeding,other',
            'priority' => 'in:low,medium,high',
            'scheduled_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['organization_id'] = auth()->user()->organization_id;
        $data['created_by'] = auth()->id();

        $job = $this->jobService->create($data);
        return response()->json(['data' => new FieldJobResource($job)], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $job = $this->jobService->getById($id);
        
        if (!$job || $job->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        $updated = $this->jobService->update($id, $request->all());
        return response()->json(['data' => new FieldJobResource($updated)]);
    }

    public function destroy(int $id): JsonResponse
    {
        $job = $this->jobService->getById($id);
        
        if (!$job || $job->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        $this->jobService->delete($id);
        return response()->json(['message' => 'Job deleted successfully']);
    }

    public function start(int $id): JsonResponse
    {
        $job = $this->jobService->startJob($id);
        return response()->json(['data' => new FieldJobResource($job)]);
    }

    public function complete(int $id): JsonResponse
    {
        $job = $this->jobService->completeJob($id);
        return response()->json(['data' => new FieldJobResource($job)]);
    }

    public function cancel(int $id): JsonResponse
    {
        $job = $this->jobService->cancelJob($id);
        return response()->json(['data' => new FieldJobResource($job)]);
    }
}
