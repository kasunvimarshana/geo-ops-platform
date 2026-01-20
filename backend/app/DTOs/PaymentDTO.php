<?php

namespace App\DTOs;

class PaymentDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $invoiceId,
        public readonly int $customerId,
        public readonly float $amount,
        public readonly string $paymentMethod,
        public readonly string $paymentDate,
        public readonly ?string $transactionId,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            invoiceId: $data['invoice_id'],
            customerId: $data['customer_id'],
            amount: (float) $data['amount'],
            paymentMethod: $data['payment_method'],
            paymentDate: $data['payment_date'] ?? now()->toDateString(),
            transactionId: $data['transaction_id'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'organization_id' => $this->organizationId,
            'invoice_id' => $this->invoiceId,
            'customer_id' => $this->customerId,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
            'payment_date' => $this->paymentDate,
            'transaction_id' => $this->transactionId,
            'notes' => $this->notes,
        ], fn($value) => $value !== null);
    }
}
