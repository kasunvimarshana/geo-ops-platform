<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Repositories\Interfaces\ExpenseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $organizationId = $request->user()->organization_id;
            
            $expenses = $this->expenseRepository->findByOrganization($organizationId, [
                'expense_type' => $request->expense_type,
                'category' => $request->category,
                'machine_id' => $request->machine_id,
                'driver_id' => $request->driver_id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'per_page' => $request->per_page ?? 15,
            ]);

            return response()->json([
                'success' => true,
                'data' => $expenses->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $expenses->total(),
                        'per_page' => $expenses->perPage(),
                        'current_page' => $expenses->currentPage(),
                        'last_page' => $expenses->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expenses',
            ], 500);
        }
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        try {
            $expense = $this->expenseRepository->create(array_merge(
                $request->validated(),
                [
                    'organization_id' => $request->user()->organization_id,
                    'recorded_by' => $request->user()->id,
                    'sync_status' => 'synced',
                ]
            ));

            return response()->json([
                'success' => true,
                'data' => $expense,
                'message' => 'Expense created successfully',
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
            $expense = $this->expenseRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$expense) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $expense,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expense',
            ], 500);
        }
    }

    public function update(int $id, UpdateExpenseRequest $request): JsonResponse
    {
        try {
            $expense = $this->expenseRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$expense) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense not found',
                ], 404);
            }

            $this->expenseRepository->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => $this->expenseRepository->findById($id),
                'message' => 'Expense updated successfully',
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
            $expense = $this->expenseRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$expense) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense not found',
                ], 404);
            }

            $this->expenseRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function summary(Request $request): JsonResponse
    {
        try {
            $summary = $this->expenseRepository->getSummary(
                $request->user()->organization_id,
                [
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expense summary',
            ], 500);
        }
    }
}
