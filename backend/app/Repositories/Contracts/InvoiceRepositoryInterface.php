<?php

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Invoice Repository Interface
 * 
 * Defines the contract for invoice data access operations.
 */
interface InvoiceRepositoryInterface
{
    /**
     * Create a new invoice record
     * 
     * @param array $data
     * @return Invoice
     */
    public function create(array $data): Invoice;

    /**
     * Update an invoice record
     * 
     * @param int $id
     * @param array $data
     * @return Invoice
     */
    public function update(int $id, array $data): Invoice;

    /**
     * Delete an invoice (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find an invoice by ID
     * 
     * @param int $id
     * @return Invoice
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Invoice;

    /**
     * Find all invoices for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @return Collection
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection;

    /**
     * Get paginated invoices for a specific organization
     * 
     * @param int $organizationId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find invoices by status
     * 
     * @param int $organizationId
     * @param string $status
     * @return Collection
     */
    public function findByStatus(int $organizationId, string $status): Collection;

    /**
     * Find invoices by customer
     * 
     * @param int $customerId
     * @return Collection
     */
    public function findByCustomer(int $customerId): Collection;

    /**
     * Find invoices by job
     * 
     * @param int $jobId
     * @return Collection
     */
    public function findByJob(int $jobId): Collection;

    /**
     * Get invoices within date range
     * 
     * @param int $organizationId
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function findByDateRange(int $organizationId, string $startDate, string $endDate): Collection;

    /**
     * Generate unique invoice number
     * 
     * @param int $organizationId
     * @return string
     */
    public function generateInvoiceNumber(int $organizationId): string;

    /**
     * Update invoice paid amount and balance
     * 
     * @param int $id
     * @param float $paymentAmount
     * @return Invoice
     */
    public function updateBalance(int $id, float $paymentAmount): Invoice;
}
