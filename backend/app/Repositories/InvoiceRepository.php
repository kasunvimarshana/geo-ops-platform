<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Invoice Repository
 * 
 * Implements the InvoiceRepositoryInterface.
 * Handles all database operations for invoices.
 */
class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * Create a new invoice record
     */
    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    /**
     * Update an invoice record
     */
    public function update(int $id, array $data): Invoice
    {
        $invoice = $this->findById($id);
        $invoice->update($data);
        return $invoice->fresh();
    }

    /**
     * Delete an invoice (soft delete)
     */
    public function delete(int $id): bool
    {
        $invoice = $this->findById($id);
        return $invoice->delete();
    }

    /**
     * Find an invoice by ID
     */
    public function findById(int $id): Invoice
    {
        return Invoice::with(['job', 'customer', 'payments', 'organization'])
            ->findOrFail($id);
    }

    /**
     * Find all invoices for a specific organization
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection
    {
        $query = Invoice::where('organization_id', $organizationId)
            ->with(['job', 'customer', 'payments']);

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated invoices for a specific organization
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Invoice::where('organization_id', $organizationId)
            ->with(['job', 'customer', 'payments']);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Find invoices by status
     */
    public function findByStatus(int $organizationId, string $status): Collection
    {
        return Invoice::where('organization_id', $organizationId)
            ->where('status', $status)
            ->with(['job', 'customer', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find invoices by customer
     */
    public function findByCustomer(int $customerId): Collection
    {
        return Invoice::where('customer_id', $customerId)
            ->with(['job', 'payments', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find invoices by job
     */
    public function findByJob(int $jobId): Collection
    {
        return Invoice::where('job_id', $jobId)
            ->with(['customer', 'payments', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get invoices within date range
     */
    public function findByDateRange(int $organizationId, string $startDate, string $endDate): Collection
    {
        return Invoice::where('organization_id', $organizationId)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->with(['job', 'customer', 'payments'])
            ->orderBy('invoice_date', 'asc')
            ->get();
    }

    /**
     * Generate unique invoice number
     */
    public function generateInvoiceNumber(int $organizationId): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "INV-{$date}-";
        
        // Get the last invoice number for today
        $lastInvoice = Invoice::where('organization_id', $organizationId)
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract the sequence number and increment
            $lastSequence = intval(substr($lastInvoice->invoice_number, -4));
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update invoice paid amount and balance
     */
    public function updateBalance(int $id, float $paymentAmount): Invoice
    {
        $invoice = $this->findById($id);
        $newPaidAmount = $invoice->paid_amount + $paymentAmount;
        $newBalance = $invoice->total_amount - $newPaidAmount;
        
        $invoice->update([
            'paid_amount' => $newPaidAmount,
            'balance_amount' => $newBalance,
            'status' => $newBalance <= 0 ? 'paid' : $invoice->status,
        ]);

        return $invoice->fresh();
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['job_id'])) {
            $query->where('job_id', $filters['job_id']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('invoice_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);
    }
}
