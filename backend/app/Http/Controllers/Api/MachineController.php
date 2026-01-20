<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Machine\StoreMachineRequest;
use App\Http\Requests\Machine\UpdateMachineRequest;
use App\Repositories\Interfaces\MachineRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MachineController extends Controller
{
    public function __construct(
        private MachineRepositoryInterface $machineRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $organizationId = $request->user()->organization_id;
            
            $machines = $this->machineRepository->findByOrganization($organizationId, [
                'is_active' => $request->is_active,
                'machine_type' => $request->machine_type,
                'search' => $request->search,
                'per_page' => $request->per_page ?? 15,
            ]);

            return response()->json([
                'success' => true,
                'data' => $machines->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $machines->total(),
                        'per_page' => $machines->perPage(),
                        'current_page' => $machines->currentPage(),
                        'last_page' => $machines->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch machines',
            ], 500);
        }
    }

    public function store(StoreMachineRequest $request): JsonResponse
    {
        try {
            $machine = $this->machineRepository->create(array_merge(
                $request->validated(),
                ['organization_id' => $request->user()->organization_id]
            ));

            return response()->json([
                'success' => true,
                'data' => $machine,
                'message' => 'Machine created successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $machine = $this->machineRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$machine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Machine not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $machine,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch machine',
            ], 500);
        }
    }

    public function update(int $id, UpdateMachineRequest $request): JsonResponse
    {
        try {
            $machine = $this->machineRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$machine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Machine not found',
                ], 404);
            }

            $this->machineRepository->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => $this->machineRepository->findById($id),
                'message' => 'Machine updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $machine = $this->machineRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$machine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Machine not found',
                ], 404);
            }

            $this->machineRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Machine deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
