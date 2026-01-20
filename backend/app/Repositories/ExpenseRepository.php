<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\Interfaces\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function create(array $data): object
    {
        return Expense::create($data);
    }

    public function findById(int $id): ?object
    {
        return Expense::with(['machine', 'driver', 'job', 'recorder'])->find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?object
    {
        return Expense::with(['machine', 'driver', 'job'])
            ->where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function findByOrganization(int $organizationId, array $filters = []): object
    {
        $query = Expense::with(['machine', 'driver', 'job'])
            ->where('organization_id', $organizationId);

        if (isset($filters['expense_type'])) {
            $query->where('expense_type', $filters['expense_type']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['machine_id'])) {
            $query->where('machine_id', $filters['machine_id']);
        }

        if (isset($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('expense_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('expense_date', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('expense_date', 'desc')->paginate($perPage);
    }

    public function update(int $id, array $data): bool
    {
        $expense = Expense::find($id);
        return $expense ? $expense->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $expense = Expense::find($id);
        return $expense ? $expense->delete() : false;
    }

    public function findByOfflineId(string $offlineId, int $organizationId): ?object
    {
        return Expense::where('offline_id', $offlineId)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function getPendingSync(int $organizationId): array
    {
        return Expense::where('organization_id', $organizationId)
            ->where('sync_status', 'pending')
            ->get()
            ->toArray();
    }

    public function getSummary(int $organizationId, array $filters = []): array
    {
        $query = Expense::where('organization_id', $organizationId);

        if (isset($filters['from_date'])) {
            $query->whereDate('expense_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('expense_date', '<=', $filters['to_date']);
        }

        $summary = $query->select(
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(*) as total_count'),
            'expense_type',
            'category'
        )
        ->groupBy('expense_type', 'category')
        ->get()
        ->toArray();

        $totalAmount = $query->sum('amount');

        return [
            'total_amount' => $totalAmount,
            'breakdown' => $summary,
        ];
    }

    public function findByMachine(int $machineId, array $filters = []): array
    {
        $query = Expense::where('machine_id', $machineId);

        if (isset($filters['from_date'])) {
            $query->whereDate('expense_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('expense_date', '<=', $filters['to_date']);
        }

        return $query->orderBy('expense_date', 'desc')->get()->toArray();
    }
}
