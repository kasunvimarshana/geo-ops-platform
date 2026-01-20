<?php

namespace App\Services;

use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\FieldJobRepositoryInterface;
use App\DTOs\Invoice\CreateInvoiceDTO;
use App\DTOs\Invoice\UpdateInvoiceDTO;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Invoice Service
 * 
 * Handles all business logic related to invoice management.
 */
class InvoiceService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private FieldJobRepositoryInterface $jobRepository
    ) {}

    /**
     * Create a new invoice
     */
    public function createInvoice(CreateInvoiceDTO $dto): Invoice
    {
        return DB::transaction(function () use ($dto) {
            $user = Auth::user();
            
            // Validate related entities if provided
            if ($dto->jobId) {
                $job = $this->jobRepository->findById($dto->jobId);
                if ($job->organization_id !== $user->organization_id) {
                    throw new \Exception('Job does not belong to your organization');
                }
            }

            if ($dto->customerId) {
                $customer = User::findOrFail($dto->customerId);
                if ($customer->organization_id !== $user->organization_id) {
                    throw new \Exception('Customer does not belong to your organization');
                }
            }

            // Generate unique invoice number
            $invoiceNumber = $this->invoiceRepository->generateInvoiceNumber($user->organization_id);
            
            // Calculate amounts from line items if provided
            $lineItems = $dto->lineItems ?? [];
            $subtotal = $this->calculateSubtotal($lineItems);
            $taxAmount = $dto->taxAmount ?? 0;
            $discountAmount = $dto->discountAmount ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;
            
            // Prepare invoice data
            $invoiceData = [
                'organization_id' => $user->organization_id,
                'job_id' => $dto->jobId,
                'customer_id' => $dto->customerId,
                'invoice_number' => $invoiceNumber,
                'status' => $dto->status,
                'customer_name' => $dto->customerName,
                'customer_phone' => $dto->customerPhone,
                'customer_address' => $dto->customerAddress,
                'invoice_date' => $dto->invoiceDate ?? Carbon::now()->toDateString(),
                'due_date' => $dto->dueDate,
                'line_items' => $lineItems,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance_amount' => $totalAmount,
                'currency' => $dto->currency,
                'notes' => $dto->notes,
                'terms' => $dto->terms,
                'is_synced' => false,
                'created_by' => $user->id,
            ];
            
            $invoice = $this->invoiceRepository->create($invoiceData);
            
            return $invoice;
        });
    }

    /**
     * Create invoice from a completed job
     */
    public function createInvoiceFromJob(int $jobId): Invoice
    {
        return DB::transaction(function () use ($jobId) {
            $user = Auth::user();
            
            $job = $this->jobRepository->findById($jobId);
            
            if ($job->organization_id !== $user->organization_id) {
                throw new \Exception('Job does not belong to your organization');
            }

            if ($job->status !== 'completed') {
                throw new \Exception('Can only create invoice from completed jobs');
            }

            // Check if invoice already exists for this job
            $existingInvoices = $this->invoiceRepository->findByJob($jobId);
            if ($existingInvoices->isNotEmpty()) {
                throw new \Exception('Invoice already exists for this job');
            }

            // Generate invoice number
            $invoiceNumber = $this->invoiceRepository->generateInvoiceNumber($user->organization_id);

            // Create line items from job
            $lineItems = [
                [
                    'description' => "{$job->service_type} service for {$job->area_acres} acres",
                    'quantity' => $job->area_acres ?? 1,
                    'unit' => $job->rate_unit,
                    'rate' => $job->rate_per_unit ?? 0,
                    'amount' => $job->actual_amount ?? $job->estimated_amount ?? 0,
                ]
            ];

            $subtotal = $job->actual_amount ?? $job->estimated_amount ?? 0;
            $totalAmount = $subtotal;

            // Create invoice
            $invoiceData = [
                'organization_id' => $user->organization_id,
                'job_id' => $jobId,
                'customer_id' => $job->customer_id,
                'invoice_number' => $invoiceNumber,
                'status' => 'pending',
                'customer_name' => $job->customer_name,
                'customer_phone' => $job->customer_phone,
                'customer_address' => $job->customer_address,
                'invoice_date' => Carbon::now()->toDateString(),
                'due_date' => Carbon::now()->addDays(30)->toDateString(),
                'line_items' => $lineItems,
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance_amount' => $totalAmount,
                'currency' => 'USD',
                'notes' => "Invoice for Job #{$job->job_number}",
                'terms' => 'Payment due within 30 days',
                'is_synced' => false,
                'created_by' => $user->id,
            ];

            $invoice = $this->invoiceRepository->create($invoiceData);

            return $invoice;
        });
    }

    /**
     * Update an existing invoice
     */
    public function updateInvoice(int $invoiceId, UpdateInvoiceDTO $dto): Invoice
    {
        return DB::transaction(function () use ($invoiceId, $dto) {
            $user = Auth::user();
            
            $invoice = $this->invoiceRepository->findById($invoiceId);
            
            if ($invoice->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to invoice');
            }

            // Validate: can't modify paid invoices
            if ($invoice->status === 'paid' && $dto->status !== 'paid') {
                throw new \Exception('Cannot modify paid invoices');
            }

            // Validate: can only modify draft invoices or change status
            $updateArray = $dto->toArray();
            if ($invoice->status !== 'draft' && count($updateArray) > 1) {
                if (!isset($updateArray['status'])) {
                    throw new \Exception('Can only modify draft invoices');
                }
            }

            $updateData = $updateArray;
            
            // Recalculate amounts if line items changed
            if (isset($updateData['line_items'])) {
                $subtotal = $this->calculateSubtotal($updateData['line_items']);
                $taxAmount = $updateData['tax_amount'] ?? $invoice->tax_amount;
                $discountAmount = $updateData['discount_amount'] ?? $invoice->discount_amount;
                $totalAmount = $subtotal + $taxAmount - $discountAmount;
                
                $updateData['subtotal'] = $subtotal;
                $updateData['total_amount'] = $totalAmount;
                $updateData['balance_amount'] = $totalAmount - $invoice->paid_amount;
            }
            
            $updateData['updated_by'] = $user->id;
            $updateData['is_synced'] = false;
            
            $invoice = $this->invoiceRepository->update($invoice->id, $updateData);
            
            return $invoice;
        });
    }

    /**
     * Get all invoices for the current organization
     */
    public function getInvoices(array $filters = [])
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->invoiceRepository->findByOrganization($organizationId, $filters);
    }

    /**
     * Get a specific invoice by ID
     */
    public function getInvoice(int $invoiceId): Invoice
    {
        $user = Auth::user();
        $invoice = $this->invoiceRepository->findById($invoiceId);
        
        if ($invoice->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to invoice');
        }
        
        return $invoice;
    }

    /**
     * Delete an invoice (soft delete)
     */
    public function deleteInvoice(int $invoiceId): bool
    {
        $user = Auth::user();
        $invoice = $this->invoiceRepository->findById($invoiceId);
        
        if ($invoice->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to invoice');
        }
        
        // Prevent deletion of paid invoices
        if ($invoice->status === 'paid') {
            throw new \Exception('Cannot delete paid invoices');
        }

        // Prevent deletion if has payments
        if ($invoice->payments()->count() > 0) {
            throw new \Exception('Cannot delete invoice with payments');
        }
        
        $result = $this->invoiceRepository->delete($invoice->id);
        
        return $result;
    }

    /**
     * Get invoices with pagination
     */
    public function getInvoicesPaginated(array $filters = [], int $perPage = 15)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->invoiceRepository->paginateByOrganization($organizationId, $filters, $perPage);
    }

    /**
     * Generate PDF for invoice (placeholder)
     */
    public function generatePDF(int $invoiceId): array
    {
        $user = Auth::user();
        $invoice = $this->invoiceRepository->findById($invoiceId);
        
        if ($invoice->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to invoice');
        }

        // TODO: Implement actual PDF generation using library like dompdf or snappy
        // For now, return a placeholder response
        return [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'pdf_url' => url("/api/v1/invoices/{$invoice->id}/pdf"),
            'message' => 'PDF generation is not yet implemented. Will use dompdf or snappy library.',
        ];
    }

    /**
     * Calculate subtotal from line items
     */
    private function calculateSubtotal(array $lineItems): float
    {
        return array_reduce($lineItems, function ($carry, $item) {
            return $carry + ($item['amount'] ?? 0);
        }, 0);
    }
}
