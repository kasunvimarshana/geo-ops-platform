<?php

namespace App\DTOs\Invoice;

use Illuminate\Http\Request;

/**
 * Update Invoice DTO
 * 
 * Data Transfer Object for updating an existing invoice.
 */
class UpdateInvoiceDTO
{
    public function __construct(
        public readonly ?string $customerName = null,
        public readonly ?string $customerPhone = null,
        public readonly ?string $customerAddress = null,
        public readonly ?string $invoiceDate = null,
        public readonly ?string $dueDate = null,
        public readonly ?array $lineItems = null,
        public readonly ?float $taxAmount = null,
        public readonly ?float $discountAmount = null,
        public readonly ?string $currency = null,
        public readonly ?string $notes = null,
        public readonly ?string $terms = null,
        public readonly ?string $status = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            customerName: $request->input('customer_name'),
            customerPhone: $request->input('customer_phone'),
            customerAddress: $request->input('customer_address'),
            invoiceDate: $request->input('invoice_date'),
            dueDate: $request->input('due_date'),
            lineItems: $request->input('line_items'),
            taxAmount: $request->input('tax_amount'),
            discountAmount: $request->input('discount_amount'),
            currency: $request->input('currency'),
            notes: $request->input('notes'),
            terms: $request->input('terms'),
            status: $request->input('status'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            customerName: $data['customer_name'] ?? null,
            customerPhone: $data['customer_phone'] ?? null,
            customerAddress: $data['customer_address'] ?? null,
            invoiceDate: $data['invoice_date'] ?? null,
            dueDate: $data['due_date'] ?? null,
            lineItems: $data['line_items'] ?? null,
            taxAmount: $data['tax_amount'] ?? null,
            discountAmount: $data['discount_amount'] ?? null,
            currency: $data['currency'] ?? null,
            notes: $data['notes'] ?? null,
            terms: $data['terms'] ?? null,
            status: $data['status'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
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
        ], fn($value) => $value !== null);
    }
}
