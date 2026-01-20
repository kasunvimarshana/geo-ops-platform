<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseService $expenseService
    ) {}

    /**
     * Get all expenses for the authenticated user's organization
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Expense::forOrganization($user->organization_id)
            ->with(['machine', 'driver', 'recorder'])
            ->orderBy('expense_date', 'desc');
        
        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }
        
        // Filter by machine
        if ($request->has('machine_id')) {
            $query->where('machine_id', $request->machine_id);
        }
        
        // Filter by driver
        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }
        
        $perPage = $request->input('per_page', 15);
        $expenses = $query->paginate($perPage);
        
        return response()->json($expenses);
    }

    /**
     * Get a single expense
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $expense = Expense::forOrganization($user->organization_id)
            ->with(['machine', 'driver', 'recorder', 'approver'])
            ->findOrFail($id);
        
        return response()->json($expense);
    }

    /**
     * Create a new expense
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'machine_id' => 'nullable|exists:machines,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'category' => 'required|in:fuel,parts,maintenance,labor,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'expense_date' => 'nullable|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $data = $validator->validated();
        $data['organization_id'] = $user->organization_id;
        $data['recorded_by'] = $user->id;
        
        $expense = $this->expenseService->create($data);
        
        // Handle receipt upload if provided
        if ($request->hasFile('receipt')) {
            $this->expenseService->uploadReceipt($expense, $request->file('receipt'));
        }
        
        return response()->json([
            'message' => 'Expense recorded successfully',
            'expense' => $expense->load(['machine', 'driver'])
        ], 201);
    }

    /**
     * Update an existing expense
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'machine_id' => 'nullable|exists:machines,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'category' => 'nullable|in:fuel,parts,maintenance,labor,other',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'expense_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $expense = Expense::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        // Don't allow editing approved expenses
        if ($expense->status === Expense::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Cannot edit approved expenses'
            ], 403);
        }
        
        $expense = $this->expenseService->update($expense, $validator->validated());
        
        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense->load(['machine', 'driver'])
        ]);
    }

    /**
     * Upload receipt for expense
     */
    public function uploadReceipt(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $expense = Expense::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        $path = $this->expenseService->uploadReceipt($expense, $request->file('receipt'));
        
        return response()->json([
            'message' => 'Receipt uploaded successfully',
            'path' => $path
        ]);
    }

    /**
     * Approve expense
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $expense = Expense::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        if ($expense->status === Expense::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Expense is already approved'
            ], 400);
        }
        
        $expense = $this->expenseService->approve($expense, $user->id);
        
        return response()->json([
            'message' => 'Expense approved successfully',
            'expense' => $expense
        ]);
    }

    /**
     * Reject expense
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $expense = Expense::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        if ($expense->status === Expense::STATUS_REJECTED) {
            return response()->json([
                'message' => 'Expense is already rejected'
            ], 400);
        }
        
        $expense = $this->expenseService->reject($expense, $user->id);
        
        return response()->json([
            'message' => 'Expense rejected',
            'expense' => $expense
        ]);
    }

    /**
     * Delete an expense
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $expense = Expense::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        // Don't allow deleting approved expenses
        if ($expense->status === Expense::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Cannot delete approved expenses'
            ], 403);
        }
        
        $this->expenseService->delete($expense);
        
        return response()->json([
            'message' => 'Expense deleted successfully'
        ]);
    }

    /**
     * Get expense summary statistics
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $period = $request->input('period', 'all');
        
        $summary = $this->expenseService->getSummary($user->organization_id, $period);
        
        return response()->json($summary);
    }

    /**
     * Get machine expenses
     */
    public function machineExpenses(Request $request, int $machineId): JsonResponse
    {
        $user = $request->user();
        
        $data = $this->expenseService->getMachineExpenses($machineId, $user->organization_id);
        
        return response()->json($data);
    }

    /**
     * Get driver expenses
     */
    public function driverExpenses(Request $request, int $driverId): JsonResponse
    {
        $user = $request->user();
        
        $data = $this->expenseService->getDriverExpenses($driverId, $user->organization_id);
        
        return response()->json($data);
    }
}
