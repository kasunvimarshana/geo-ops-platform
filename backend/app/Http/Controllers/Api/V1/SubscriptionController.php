<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Subscription\CheckLimitDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CheckLimitRequest;
use App\Http\Resources\SubscriptionPackageResource;
use App\Http\Resources\UsageStatsResource;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * List all available subscription packages
     */
    public function packages(): JsonResponse
    {
        try {
            $packages = $this->subscriptionService->getAllPackages();

            return $this->successResponse(
                SubscriptionPackageResource::collection($packages),
                'Subscription packages retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve subscription packages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve subscription packages.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get current organization's subscription with usage stats
     */
    public function current(): JsonResponse
    {
        try {
            $organization = auth()->user()->organization;
            
            $subscription = $this->subscriptionService->getCurrentSubscription($organization);

            return $this->successResponse(
                new UsageStatsResource($subscription),
                'Current subscription retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve current subscription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve current subscription.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Check if organization can perform an action
     */
    public function checkLimit(CheckLimitRequest $request): JsonResponse
    {
        try {
            $dto = CheckLimitDTO::fromArray($request->validated());
            $organization = auth()->user()->organization;
            
            $result = $this->subscriptionService->checkLimit(
                $organization,
                $dto->resource,
                $dto->count
            );

            return $this->successResponse(
                $result,
                $result['can_perform'] 
                    ? 'Action is allowed.' 
                    : 'Action would exceed package limits.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to check subscription limit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to check subscription limit.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
