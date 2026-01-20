<?php

namespace App\DTOs;

class InvoiceDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $jobId,
        public readonly int $customerId,
        public readonly string $invoiceNumber,
        public readonly string $invoiceDate,
        public readonly float $subtotal,
        public readonly float $taxAmount,
        public readonly float $discountAmount,
        public readonly float $totalAmount,
        public readonly string $status,
        public readonly ?string $dueDate,
        public readonly ?string $notes,
        public readonly ?string $pdfPath,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            jobId: $data['job_id'],
            customerId: $data['customer_id'],
            invoiceNumber: $data['invoice_number'],
            invoiceDate: $data['invoice_date'],
            subtotal: (float) $data['subtotal'],
            taxAmount: (float) ($data['tax_amount'] ?? 0),
            discountAmount: (float) ($data['discount_amount'] ?? 0),
            totalAmount: (float) $data['total_amount'],
            status: $data['status'] ?? 'draft',
            dueDate: $data['due_date'] ?? null,
            notes: $data['notes'] ?? null,
            pdfPath: $data['pdf_path'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'organization_id' => $this->organizationId,
            'job_id' => $this->jobId,
            'customer_id' => $this->customerId,
            'invoice_number' => $this->invoiceNumber,
            'invoice_date' => $this->invoiceDate,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'discount_amount' => $this->discountAmount,
            'total_amount' => $this->totalAmount,
            'status' => $this->status,
            'due_date' => $this->dueDate,
            'notes' => $this->notes,
            'pdf_path' => $this->pdfPath,
        ], fn($value) => $value !== null);
    }
}
