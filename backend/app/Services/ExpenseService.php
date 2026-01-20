<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
    /**
     * Create expense
     */
    public function create(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            return Expense::create([
                'organization_id' => $data['organization_id'],
                'machine_id' => $data['machine_id'] ?? null,
                'driver_id' => $data['driver_id'] ?? null,
                'category' => $data['category'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
                'receipt_path' => $data['receipt_path'] ?? null,
                'expense_date' => $data['expense_date'] ?? now(),
                'status' => $data['status'] ?? Expense::STATUS_PENDING,
                'recorded_by' => $data['recorded_by'] ?? null,
            ]);
        });
    }
    
    /**
     * Update expense
     */
    public function update(Expense $expense, array $data): Expense
    {
        $expense->update([
            'machine_id' => $data['machine_id'] ?? $expense->machine_id,
            'driver_id' => $data['driver_id'] ?? $expense->driver_id,
            'category' => $data['category'] ?? $expense->category,
            'amount' => $data['amount'] ?? $expense->amount,
            'description' => $data['description'] ?? $expense->description,
            'expense_date' => $data['expense_date'] ?? $expense->expense_date,
        ]);
        
        return $expense->fresh();
    }
    
    /**
     * Upload receipt
     */
    public function uploadReceipt(Expense $expense, $file): string
    {
        // Delete old receipt if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
        
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs(
            "receipts/{$expense->organization_id}",
            $filename,
            'public'
        );
        
        $expense->update(['receipt_path' => $path]);
        
        return $path;
    }
    
    /**
     * Approve expense
     */
    public function approve(Expense $expense, int $approverId): Expense
    {
        $expense->update([
            'status' => Expense::STATUS_APPROVED,
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
        
        return $expense->fresh();
    }
    
    /**
     * Reject expense
     */
    public function reject(Expense $expense, int $approverId): Expense
    {
        $expense->update([
            'status' => Expense::STATUS_REJECTED,
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
        
        return $expense->fresh();
    }
    
    /**
     * Delete expense
     */
    public function delete(Expense $expense): bool
    {
        // Delete receipt file if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
        
        return $expense->delete();
    }
    
    /**
     * Get expense summary
     */
    public function getSummary(int $organizationId, ?string $period = 'all'): array
    {
        $query = Expense::forOrganization($organizationId);
        
        // Apply period filter
        switch ($period) {
            case 'today':
                $query->whereDate('expense_date', today());
                break;
            case 'this_week':
                $query->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('expense_date', now()->month)
                      ->whereYear('expense_date', now()->year);
                break;
            case 'this_year':
                $query->whereYear('expense_date', now()->year);
                break;
        }
        
        $expenses = $query->get();
        
        return [
            'total_count' => $expenses->count(),
            'total_amount' => $expenses->sum('amount'),
            'pending_count' => $expenses->where('status', Expense::STATUS_PENDING)->count(),
            'approved_count' => $expenses->where('status', Expense::STATUS_APPROVED)->count(),
            'rejected_count' => $expenses->where('status', Expense::STATUS_REJECTED)->count(),
            'by_category' => [
                'fuel' => $expenses->where('category', Expense::CATEGORY_FUEL)->sum('amount'),
                'parts' => $expenses->where('category', Expense::CATEGORY_PARTS)->sum('amount'),
                'maintenance' => $expenses->where('category', Expense::CATEGORY_MAINTENANCE)->sum('amount'),
                'labor' => $expenses->where('category', Expense::CATEGORY_LABOR)->sum('amount'),
                'other' => $expenses->where('category', Expense::CATEGORY_OTHER)->sum('amount'),
            ],
            'period' => $period,
        ];
    }
    
    /**
     * Get machine expenses
     */
    public function getMachineExpenses(int $machineId, int $organizationId): array
    {
        $expenses = Expense::where('machine_id', $machineId)
            ->where('organization_id', $organizationId)
            ->orderBy('expense_date', 'desc')
            ->get();
        
        return [
            'expenses' => $expenses,
            'total_amount' => $expenses->sum('amount'),
            'by_category' => [
                'fuel' => $expenses->where('category', Expense::CATEGORY_FUEL)->sum('amount'),
                'parts' => $expenses->where('category', Expense::CATEGORY_PARTS)->sum('amount'),
                'maintenance' => $expenses->where('category', Expense::CATEGORY_MAINTENANCE)->sum('amount'),
                'labor' => $expenses->where('category', Expense::CATEGORY_LABOR)->sum('amount'),
                'other' => $expenses->where('category', Expense::CATEGORY_OTHER)->sum('amount'),
            ],
        ];
    }
    
    /**
     * Get driver expenses
     */
    public function getDriverExpenses(int $driverId, int $organizationId): array
    {
        $expenses = Expense::where('driver_id', $driverId)
            ->where('organization_id', $organizationId)
            ->orderBy('expense_date', 'desc')
            ->get();
        
        return [
            'expenses' => $expenses,
            'total_amount' => $expenses->sum('amount'),
            'by_category' => [
                'fuel' => $expenses->where('category', Expense::CATEGORY_FUEL)->sum('amount'),
                'parts' => $expenses->where('category', Expense::CATEGORY_PARTS)->sum('amount'),
                'maintenance' => $expenses->where('category', Expense::CATEGORY_MAINTENANCE)->sum('amount'),
                'labor' => $expenses->where('category', Expense::CATEGORY_LABOR)->sum('amount'),
                'other' => $expenses->where('category', Expense::CATEGORY_OTHER)->sum('amount'),
            ],
        ];
    }
}
