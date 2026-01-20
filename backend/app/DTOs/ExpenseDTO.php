<?php

namespace App\DTOs;

class ExpenseDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly string $category,
        public readonly float $amount,
        public readonly string $expenseDate,
        public readonly ?int $driverId,
        public readonly ?int $machineId,
        public readonly ?int $jobId,
        public readonly ?string $description,
        public readonly ?string $receiptPath,
        public readonly string $status,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            category: $data['category'],
            amount: (float) $data['amount'],
            expenseDate: $data['expense_date'] ?? now()->toDateString(),
            driverId: $data['driver_id'] ?? null,
            machineId: $data['machine_id'] ?? null,
            jobId: $data['job_id'] ?? null,
            description: $data['description'] ?? null,
            receiptPath: $data['receipt_path'] ?? null,
            status: $data['status'] ?? 'pending',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'organization_id' => $this->organizationId,
            'category' => $this->category,
            'amount' => $this->amount,
            'expense_date' => $this->expenseDate,
            'driver_id' => $this->driverId,
            'machine_id' => $this->machineId,
            'job_id' => $this->jobId,
            'description' => $this->description,
            'receipt_path' => $this->receiptPath,
            'status' => $this->status,
        ], fn($value) => $value !== null);
    }
}
