<?php

namespace App\DTOs\Invoice;

use Illuminate\Http\Request;

/**
 * Create Invoice DTO
 * 
 * Data Transfer Object for creating a new invoice.
 */
class CreateInvoiceDTO
{
    public function __construct(
        public readonly ?int $jobId,
        public readonly ?int $customerId,
        public readonly string $customerName,
        public readonly ?string $customerPhone = null,
        public readonly ?string $customerAddress = null,
        public readonly ?string $invoiceDate = null,
        public readonly ?string $dueDate = null,
        public readonly ?array $lineItems = null,
        public readonly ?float $taxAmount = null,
        public readonly ?float $discountAmount = null,
        public readonly string $currency = 'USD',
        public readonly ?string $notes = null,
        public readonly ?string $terms = null,
        public readonly string $status = 'draft',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            jobId: $request->input('job_id'),
            customerId: $request->input('customer_id'),
            customerName: $request->input('customer_name'),
            customerPhone: $request->input('customer_phone'),
            customerAddress: $request->input('customer_address'),
            invoiceDate: $request->input('invoice_date'),
            dueDate: $request->input('due_date'),
            lineItems: $request->input('line_items'),
            taxAmount: $request->input('tax_amount'),
            discountAmount: $request->input('discount_amount'),
            currency: $request->input('currency', 'USD'),
            notes: $request->input('notes'),
            terms: $request->input('terms'),
            status: $request->input('status', 'draft'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            jobId: $data['job_id'] ?? null,
            customerId: $data['customer_id'] ?? null,
            customerName: $data['customer_name'],
            customerPhone: $data['customer_phone'] ?? null,
            customerAddress: $data['customer_address'] ?? null,
            invoiceDate: $data['invoice_date'] ?? null,
            dueDate: $data['due_date'] ?? null,
            lineItems: $data['line_items'] ?? null,
            taxAmount: $data['tax_amount'] ?? null,
            discountAmount: $data['discount_amount'] ?? null,
            currency: $data['currency'] ?? 'USD',
            notes: $data['notes'] ?? null,
            terms: $data['terms'] ?? null,
            status: $data['status'] ?? 'draft',
        );
    }

    public function toArray(): array
    {
        return [
            'job_id' => $this->jobId,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'customer_address' => $this->customerAddress,
            'invoice_date' => $this->invoiceDate,
            'due_date' => $this->dueDate,
            'line_items' => $this->lineItems,
            'tax_amount' => $this->taxAmount,
            'discount_amount' => $this->discountAmount,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'status' => $this->status,
        ];
    }
}
