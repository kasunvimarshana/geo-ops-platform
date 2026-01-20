<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SyncController extends Controller
{
    public function __construct(
        private SyncService $syncService
    ) {}

    public function bulkSync(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.entity_type' => 'required|in:land,job,invoice,expense,payment',
                'items.*.offline_id' => 'required|uuid',
                'items.*.data' => 'required|array',
                'items.*.updated_at' => 'required|date',
            ]);

            $results = $this->syncService->bulkSync(
                $validated,
                $request->user()->organization_id,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Sync completed',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function status(Request $request): JsonResponse
    {
        try {
            $status = $this->syncService->getSyncStatus($request->user()->organization_id);

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sync status',
            ], 500);
        }
    }

    public function resolveConflict(int $id, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'resolution' => 'required|in:use_server,use_client',
            ]);

            $result = $this->syncService->resolveConflict(
                $id,
                $validated['resolution'],
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Conflict resolved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
