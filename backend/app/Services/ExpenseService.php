<?php

namespace App\Services;

use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\FieldJobRepositoryInterface;
use App\DTOs\Expense\CreateExpenseDTO;
use App\DTOs\Expense\UpdateExpenseDTO;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Expense Service
 * 
 * Handles all business logic related to expense management.
 */
class ExpenseService
{
    public function __construct(
        private ExpenseRepositoryInterface $expenseRepository,
        private FieldJobRepositoryInterface $jobRepository
    ) {}

    /**
     * Create a new expense
     */
    public function createExpense(CreateExpenseDTO $dto): Expense
    {
        return DB::transaction(function () use ($dto) {
            $user = Auth::user();
            
            if ($dto->jobId) {
                $job = $this->jobRepository->findById($dto->jobId);
                if ($job->organization_id !== $user->organization_id) {
                    throw new \Exception('Job does not belong to your organization');
                }
            }

            if ($dto->driverId) {
                $driver = User::findOrFail($dto->driverId);
                if ($driver->organization_id !== $user->organization_id) {
                    throw new \Exception('Driver does not belong to your organization');
                }
            }

            $expenseNumber = $this->expenseRepository->generateExpenseNumber($user->organization_id);
            
            $expenseData = [
                'organization_id' => $user->organization_id,
                'job_id' => $dto->jobId,
                'driver_id' => $dto->driverId,
                'expense_number' => $expenseNumber,
                'category' => $dto->category,
                'amount' => $dto->amount,
                'currency' => $dto->currency,
                'expense_date' => $dto->expenseDate,
                'vendor_name' => $dto->vendorName,
                'description' => $dto->description,
                'receipt_path' => $dto->receiptPath,
                'attachments' => $dto->attachments,
                'is_synced' => false,
                'created_by' => $user->id,
            ];
            
            $expense = $this->expenseRepository->create($expenseData);
            
            return $expense;
        });
    }

    /**
     * Update an existing expense
     */
    public function updateExpense(int $expenseId, UpdateExpenseDTO $dto): Expense
    {
        return DB::transaction(function () use ($expenseId, $dto) {
            $user = Auth::user();
            
            $expense = $this->expenseRepository->findById($expenseId);
            
            if ($expense->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to expense');
            }

            if ($dto->jobId) {
                $job = $this->jobRepository->findById($dto->jobId);
                if ($job->organization_id !== $user->organization_id) {
                    throw new \Exception('Job does not belong to your organization');
                }
            }

            if ($dto->driverId) {
                $driver = User::findOrFail($dto->driverId);
                if ($driver->organization_id !== $user->organization_id) {
                    throw new \Exception('Driver does not belong to your organization');
                }
            }

            $updateData = $dto->toArray();
            $updateData['updated_by'] = $user->id;
            $updateData['is_synced'] = false;
            
            $expense = $this->expenseRepository->update($expense->id, $updateData);
            
            return $expense;
        });
    }

    /**
     * Get all expenses for the current organization
     */
    public function getExpenses(array $filters = [])
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->expenseRepository->findByOrganization($organizationId, $filters);
    }

    /**
     * Get a specific expense by ID
     */
    public function getExpense(int $expenseId): Expense
    {
        $user = Auth::user();
        $expense = $this->expenseRepository->findById($expenseId);
        
        if ($expense->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to expense');
        }
        
        return $expense;
    }

    /**
     * Delete an expense (soft delete)
     */
    public function deleteExpense(int $expenseId): bool
    {
        $user = Auth::user();
        $expense = $this->expenseRepository->findById($expenseId);
        
        if ($expense->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to expense');
        }
        
        $result = $this->expenseRepository->delete($expense->id);
        
        return $result;
    }

    /**
     * Get expenses with pagination
     */
    public function getExpensesPaginated(array $filters = [], int $perPage = 15)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->expenseRepository->paginateByOrganization($organizationId, $filters, $perPage);
    }

    /**
     * Get expense totals by category
     */
    public function getExpenseTotalsByCategory(?string $startDate = null, ?string $endDate = null): array
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->expenseRepository->calculateTotalsByCategory($organizationId, $startDate, $endDate);
    }

    /**
     * Get expenses by category
     */
    public function getExpensesByCategory(string $category): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->expenseRepository->findByCategory($organizationId, $category);
    }

    /**
     * Get expenses by driver
     */
    public function getExpensesByDriver(int $driverId): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        
        $driver = User::findOrFail($driverId);
        if ($driver->organization_id !== $user->organization_id) {
            throw new \Exception('Driver does not belong to your organization');
        }
        
        return $this->expenseRepository->findByDriver($driverId);
    }

    /**
     * Get expenses by job
     */
    public function getExpensesByJob(int $jobId): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        
        $job = $this->jobRepository->findById($jobId);
        if ($job->organization_id !== $user->organization_id) {
            throw new \Exception('Job does not belong to your organization');
        }
        
        return $this->expenseRepository->findByJob($jobId);
    }
}
