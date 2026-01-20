<?php

namespace App\DTOs\Payment;

use Illuminate\Http\Request;

/**
 * Create Payment DTO
 * 
 * Data Transfer Object for creating a new payment.
 */
class CreatePaymentDTO
{
    public function __construct(
        public readonly int $invoiceId,
        public readonly float $amount,
        public readonly string $paymentMethod,
        public readonly ?string $paymentDate = null,
        public readonly ?string $referenceNumber = null,
        public readonly ?string $notes = null,
        public readonly string $currency = 'USD',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            invoiceId: $request->input('invoice_id'),
            amount: $request->input('amount'),
            paymentMethod: $request->input('payment_method'),
            paymentDate: $request->input('payment_date'),
            referenceNumber: $request->input('reference_number'),
            notes: $request->input('notes'),
            currency: $request->input('currency', 'USD'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            invoiceId: $data['invoice_id'],
            amount: $data['amount'],
            paymentMethod: $data['payment_method'],
            paymentDate: $data['payment_date'] ?? null,
            referenceNumber: $data['reference_number'] ?? null,
            notes: $data['notes'] ?? null,
            currency: $data['currency'] ?? 'USD',
        );
    }

    public function toArray(): array
    {
        return [
            'invoice_id' => $this->invoiceId,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
            'payment_date' => $this->paymentDate,
            'reference_number' => $this->referenceNumber,
            'notes' => $this->notes,
            'currency' => $this->currency,
        ];
    }
}
