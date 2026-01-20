<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function current(Request $request): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->getCurrentSubscription(
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $subscription,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription',
            ], 500);
        }
    }

    public function checkFeature(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'feature' => 'required|string|in:export_pdf,generate_reports',
            ]);

            $allowed = $this->subscriptionService->checkFeature(
                $request->user()->organization_id,
                $validated['feature']
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'feature' => $validated['feature'],
                    'allowed' => $allowed,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check feature',
            ], 500);
        }
    }

    public function checkLimit(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'limit_type' => 'required|string|in:users,machines,lands',
            ]);

            $limit = $this->subscriptionService->checkLimit(
                $request->user()->organization_id,
                $validated['limit_type']
            );

            return response()->json([
                'success' => true,
                'data' => $limit,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check limit',
            ], 500);
        }
    }
}
