<?php

namespace App\DTOs\Expense;

use Illuminate\Http\Request;

/**
 * Update Expense DTO
 * 
 * Data Transfer Object for updating an existing expense.
 */
class UpdateExpenseDTO
{
    public function __construct(
        public readonly ?int $jobId = null,
        public readonly ?int $driverId = null,
        public readonly ?string $category = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $expenseDate = null,
        public readonly ?string $description = null,
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
            amount: $request->filled('amount') ? (float) $request->input('amount') : null,
            currency: $request->input('currency'),
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
            category: $data['category'] ?? null,
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            currency: $data['currency'] ?? null,
            expenseDate: $data['expense_date'] ?? null,
            description: $data['description'] ?? null,
            vendorName: $data['vendor_name'] ?? null,
            receiptPath: $data['receipt_path'] ?? null,
            attachments: $data['attachments'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [];
        
        if ($this->jobId !== null) $data['job_id'] = $this->jobId;
        if ($this->driverId !== null) $data['driver_id'] = $this->driverId;
        if ($this->category !== null) $data['category'] = $this->category;
        if ($this->amount !== null) $data['amount'] = $this->amount;
        if ($this->currency !== null) $data['currency'] = $this->currency;
        if ($this->expenseDate !== null) $data['expense_date'] = $this->expenseDate;
        if ($this->description !== null) $data['description'] = $this->description;
        if ($this->vendorName !== null) $data['vendor_name'] = $this->vendorName;
        if ($this->receiptPath !== null) $data['receipt_path'] = $this->receiptPath;
        if ($this->attachments !== null) $data['attachments'] = $this->attachments;
        
        return $data;
    }
}
