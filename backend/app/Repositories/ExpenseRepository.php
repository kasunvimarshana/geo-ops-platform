<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Expense Repository
 * 
 * Implements the ExpenseRepositoryInterface.
 * Handles all database operations for expenses.
 */
class ExpenseRepository implements ExpenseRepositoryInterface
{
    /**
     * Create a new expense record
     */
    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    /**
     * Update an expense record
     */
    public function update(int $id, array $data): Expense
    {
        $expense = $this->findById($id);
        $expense->update($data);
        return $expense->fresh();
    }

    /**
     * Delete an expense (soft delete)
     */
    public function delete(int $id): bool
    {
        $expense = $this->findById($id);
        return $expense->delete();
    }

    /**
     * Find an expense by ID
     */
    public function findById(int $id): Expense
    {
        return Expense::with(['job', 'driver', 'organization'])
            ->findOrFail($id);
    }

    /**
     * Find all expenses for a specific organization
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection
    {
        $query = Expense::where('organization_id', $organizationId)
            ->with(['job', 'driver']);

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated expenses for a specific organization
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Expense::where('organization_id', $organizationId)
            ->with(['job', 'driver']);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Find expenses by category
     */
    public function findByCategory(int $organizationId, string $category): Collection
    {
        return Expense::where('organization_id', $organizationId)
            ->where('category', $category)
            ->with(['job', 'driver'])
            ->orderBy('expense_date', 'desc')
            ->get();
    }

    /**
     * Find expenses by driver
     */
    public function findByDriver(int $driverId): Collection
    {
        return Expense::where('driver_id', $driverId)
            ->with(['job', 'organization'])
            ->orderBy('expense_date', 'desc')
            ->get();
    }

    /**
     * Find expenses by job
     */
    public function findByJob(int $jobId): Collection
    {
        return Expense::where('job_id', $jobId)
            ->with(['driver', 'organization'])
            ->orderBy('expense_date', 'desc')
            ->get();
    }

    /**
     * Get expenses within date range
     */
    public function findByDateRange(int $organizationId, string $startDate, string $endDate): Collection
    {
        return Expense::where('organization_id', $organizationId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->with(['job', 'driver'])
            ->orderBy('expense_date', 'asc')
            ->get();
    }

    /**
     * Generate unique expense number
     */
    public function generateExpenseNumber(int $organizationId): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "EXP-{$date}-";
        
        $lastExpense = Expense::where('organization_id', $organizationId)
            ->where('expense_number', 'like', "{$prefix}%")
            ->orderBy('expense_number', 'desc')
            ->first();

        if ($lastExpense) {
            $lastSequence = intval(substr($lastExpense->expense_number, -4));
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total expenses by category
     */
    public function calculateTotalsByCategory(int $organizationId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Expense::where('organization_id', $organizationId);

        if ($startDate && $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }

        $totals = $query->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        $result = [];
        foreach ($totals as $total) {
            $result[$total->category] = [
                'total' => (float) $total->total,
                'count' => $total->count,
            ];
        }

        return $result;
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (isset($filters['job_id'])) {
            $query->where('job_id', $filters['job_id']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('expense_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%");
            });
        }

        $sortBy = $filters['sort_by'] ?? 'expense_date';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);
    }
}
