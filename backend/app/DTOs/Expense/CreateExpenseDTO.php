<?php

namespace App\DTOs\Expense;

use Illuminate\Http\Request;

/**
 * Create Expense DTO
 * 
 * Data Transfer Object for creating a new expense.
 */
class CreateExpenseDTO
{
    public function __construct(
        public readonly ?int $jobId,
        public readonly ?int $driverId,
        public readonly string $category,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $expenseDate,
        public readonly string $description,
        public readonly ?string $vendorName = null,
        public readonly ?string $receiptPath = null,
        public readonly ?array $attachments = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            jobId: $request->input('job_id'),
            driverId: $request->input('driver_id'),
            category: $request->input('category'),
            amount: (float) $request->input('amount'),
            currency: $request->input('currency', 'USD'),
            expenseDate: $request->input('expense_date'),
            description: $request->input('description'),
            vendorName: $request->input('vendor_name'),
            receiptPath: $request->input('receipt_path'),
            attachments: $request->input('attachments'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            jobId: $data['job_id'] ?? null,
            driverId: $data['driver_id'] ?? null,
            category: $data['category'],
            amount: (float) $data['amount'],
            currency: $data['currency'] ?? 'USD',
            expenseDate: $data['expense_date'],
            description: $data['description'],
            vendorName: $data['vendor_name'] ?? null,
            receiptPath: $data['receipt_path'] ?? null,
            attachments: $data['attachments'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'job_id' => $this->jobId,
            'driver_id' => $this->driverId,
            'category' => $this->category,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'expense_date' => $this->expenseDate,
            'description' => $this->description,
            'vendor_name' => $this->vendorName,
            'receipt_path' => $this->receiptPath,
            'attachments' => $this->attachments,
        ];
    }
}
