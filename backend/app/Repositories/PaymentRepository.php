<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Payment Repository
 * 
 * Implements the PaymentRepositoryInterface.
 * Handles all database operations for payments.
 */
class PaymentRepository implements PaymentRepositoryInterface
{
    /**
     * Create a new payment record
     */
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    /**
     * Update a payment record
     */
    public function update(int $id, array $data): Payment
    {
        $payment = $this->findById($id);
        $payment->update($data);
        return $payment->fresh();
    }

    /**
     * Delete a payment (soft delete)
     */
    public function delete(int $id): bool
    {
        $payment = $this->findById($id);
        return $payment->delete();
    }

    /**
     * Find a payment by ID
     */
    public function findById(int $id): Payment
    {
        return Payment::with(['invoice', 'customer', 'organization'])
            ->findOrFail($id);
    }

    /**
     * Find all payments for a specific organization
     */
    public function findByOrganization(int $organizationId, array $filters = []): Collection
    {
        $query = Payment::where('organization_id', $organizationId)
            ->with(['invoice', 'customer']);

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated payments for a specific organization
     */
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Payment::where('organization_id', $organizationId)
            ->with(['invoice', 'customer']);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Find payments by invoice
     */
    public function findByInvoice(int $invoiceId): Collection
    {
        return Payment::where('invoice_id', $invoiceId)
            ->with(['customer', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments by customer
     */
    public function findByCustomer(int $customerId): Collection
    {
        return Payment::where('customer_id', $customerId)
            ->with(['invoice', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments by payment method
     */
    public function findByPaymentMethod(int $organizationId, string $method): Collection
    {
        return Payment::where('organization_id', $organizationId)
            ->where('payment_method', $method)
            ->with(['invoice', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get payments within date range
     */
    public function findByDateRange(int $organizationId, string $startDate, string $endDate): Collection
    {
        return Payment::where('organization_id', $organizationId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with(['invoice', 'customer'])
            ->orderBy('payment_date', 'asc')
            ->get();
    }

    /**
     * Generate unique payment number
     */
    public function generatePaymentNumber(int $organizationId): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "PAY-{$date}-";
        
        // Get the last payment number for today
        $lastPayment = Payment::where('organization_id', $organizationId)
            ->where('payment_number', 'like', "{$prefix}%")
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPayment) {
            // Extract the sequence number and increment
            $lastSequence = intval(substr($lastPayment->payment_number, -4));
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['invoice_id'])) {
            $query->where('invoice_id', $filters['invoice_id']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('payment_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);
    }
}
