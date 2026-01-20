<?php

namespace App\Repositories\Contracts;

use App\Models\Expense;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Expense Repository Interface
 * 
 * Defines the contract for expense data access operations.
 */
interface ExpenseRepositoryInterface
{
    /**
     * Create a new expense record
     */
    public function create(array $data): Expense;

    /**
     * Update an expense record
     */
    public function update(int $id, array $data): Expense;

    /**
     * Delete an expense (soft delete)
     */
    public function delete(int $id): bool;

    /**
     * Find an expense by ID
     */
    public function findById(int $id): Expense;

    /**
     * Find all expenses for a specific organization
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection;

    /**
     * Get paginated expenses for a specific organization
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find expenses by category
     */
    public function findByCategory(int $organizationId, string $category): Collection;

    /**
     * Find expenses by driver
     */
    public function findByDriver(int $driverId): Collection;

    /**
     * Find expenses by job
     */
    public function findByJob(int $jobId): Collection;

    /**
     * Get expenses within date range
     */
    public function findByDateRange(int $organizationId, string $startDate, string $endDate): Collection;

    /**
     * Generate unique expense number
     */
    public function generateExpenseNumber(int $organizationId): string;

    /**
     * Calculate total expenses by category
     */
    public function calculateTotalsByCategory(int $organizationId, ?string $startDate = null, ?string $endDate = null): array;
}
