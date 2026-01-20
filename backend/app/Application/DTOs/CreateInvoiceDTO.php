<?php

declare(strict_types=1);

namespace App\Application\DTOs;

class CreateInvoiceDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $fieldJobId,
        public readonly string $customerName,
        public readonly ?string $customerEmail,
        public readonly ?string $customerPhone,
        public readonly float $subtotal,
        public readonly float $taxAmount,
        public readonly float $discountAmount,
        public readonly float $totalAmount,
        public readonly string $currency,
        public readonly string $status,
        public readonly string $issuedAt,
        public readonly ?string $dueDate,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            fieldJobId: $data['field_job_id'],
            customerName: $data['customer_name'],
            customerEmail: $data['customer_email'] ?? null,
            customerPhone: $data['customer_phone'] ?? null,
            subtotal: $data['subtotal'],
            taxAmount: $data['tax_amount'] ?? 0.00,
            discountAmount: $data['discount_amount'] ?? 0.00,
            totalAmount: $data['total_amount'],
            currency: $data['currency'] ?? 'LKR',
            status: $data['status'] ?? 'draft',
            issuedAt: $data['issued_at'],
            dueDate: $data['due_date'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'organization_id' => $this->organizationId,
            'field_job_id' => $this->fieldJobId,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'discount_amount' => $this->discountAmount,
            'total_amount' => $this->totalAmount,
            'currency' => $this->currency,
            'status' => $this->status,
            'issued_at' => $this->issuedAt,
            'due_date' => $this->dueDate,
            'notes' => $this->notes,
        ];
    }
}
