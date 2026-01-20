<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Payment Repository Interface
 * 
 * Defines the contract for payment data access operations.
 */
interface PaymentRepositoryInterface
{
    /**
     * Create a new payment record
     * 
     * @param array $data
     * @return Payment
     */
    public function create(array $data): Payment;

    /**
     * Update a payment record
     * 
     * @param int $id
     * @param array $data
     * @return Payment
     */
    public function update(int $id, array $data): Payment;

    /**
     * Delete a payment (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find a payment by ID
     * 
     * @param int $id
     * @return Payment
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Payment;

    /**
     * Find all payments for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @return Collection
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection;

    /**
     * Get paginated payments for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find payments by invoice
     * 
     * @param int $invoiceId
     * @return Collection
     */
    public function findByInvoice(int $invoiceId): Collection;

    /**
     * Find payments by customer
     * 
     * @param int $customerId
     * @return Collection
     */
    public function findByCustomer(int $customerId): Collection;

    /**
     * Find payments by payment method
     * 
     * @param int $organizationId
     * @param string $method
     * @return Collection
     */
    public function findByPaymentMethod(int $organizationId, string $method): Collection;

    /**
     * Get payments within date range
     * 
     * @param int $organizationId
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function findByDateRange(int $organizationId, string $startDate, string $endDate): Collection;

    /**
     * Generate unique payment number
     * 
     * @param int $organizationId
     * @return string
     */
    public function generatePaymentNumber(int $organizationId): string;
}
