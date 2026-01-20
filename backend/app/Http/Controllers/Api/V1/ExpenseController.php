<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Expense\CreateExpenseDTO;
use App\DTOs\Expense\UpdateExpenseDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Expense Controller
 *
 * Handles expense management endpoints.
 */
class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    /**
     * List expenses with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'category' => $request->input('category'),
                'driver_id' => $request->input('driver_id'),
                'job_id' => $request->input('job_id'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by', 'expense_date'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->input('per_page', 15);
            $expenses = $this->expenseService->getExpensesPaginated($filters, $perPage);

            return $this->successResponse(
                ExpenseResource::collection($expenses)->response()->getData(true),
                'Expenses retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve expenses', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve expenses.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create new expense
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        try {
            $dto = CreateExpenseDTO::fromArray($request->validated());
            $expense = $this->expenseService->createExpense($dto);

            return $this->successResponse(
                new ExpenseResource($expense),
                'Expense created successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            \Log::error('Failed to create expense', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to create expense: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get single expense with details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $expense = $this->expenseService->getExpense($id);
            $expense->load(['job', 'driver']);

            return $this->successResponse(
                new ExpenseResource($expense),
                'Expense retrieved successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Expense not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve expense', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve expense: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update expense
     */
    public function update(UpdateExpenseRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateExpenseDTO::fromArray($request->validated());
            $expense = $this->expenseService->updateExpense($id, $dto);

            return $this->successResponse(
                new ExpenseResource($expense),
                'Expense updated successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Expense not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to update expense', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to update expense: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Soft delete expense
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->expenseService->deleteExpense($id);

            return $this->successResponse(
                null,
                'Expense deleted successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Expense not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to delete expense', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to delete expense: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get expense totals by category
     */
    public function totals(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $totals = $this->expenseService->getExpenseTotalsByCategory($startDate, $endDate);

            return $this->successResponse(
                $totals,
                'Expense totals retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve expense totals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve expense totals.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    protected function successResponse(mixed $data, string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message, int $status = Response::HTTP_BAD_REQUEST, ?array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
