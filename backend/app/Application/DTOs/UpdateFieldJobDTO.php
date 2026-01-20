<?php

declare(strict_types=1);

namespace App\Application\DTOs;

class UpdateFieldJobDTO
{
    public function __construct(
        public readonly ?int $landPlotId,
        public readonly ?int $driverId,
        public readonly ?string $customerName,
        public readonly ?string $customerPhone,
        public readonly ?string $customerAddress,
        public readonly ?string $jobType,
        public readonly ?string $status,
        public readonly ?string $priority,
        public readonly ?string $scheduledDate,
        public readonly ?string $startTime,
        public readonly ?string $endTime,
        public readonly ?float $durationHours,
        public readonly ?float $ratePerUnit,
        public readonly ?float $totalAmount,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            landPlotId: $data['land_plot_id'] ?? null,
            driverId: $data['driver_id'] ?? null,
            customerName: $data['customer_name'] ?? null,
            customerPhone: $data['customer_phone'] ?? null,
            customerAddress: $data['customer_address'] ?? null,
            jobType: $data['job_type'] ?? null,
            status: $data['status'] ?? null,
            priority: $data['priority'] ?? null,
            scheduledDate: $data['scheduled_date'] ?? null,
            startTime: $data['start_time'] ?? null,
            endTime: $data['end_time'] ?? null,
            durationHours: $data['duration_hours'] ?? null,
            ratePerUnit: $data['rate_per_unit'] ?? null,
            totalAmount: $data['total_amount'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'land_plot_id' => $this->landPlotId,
            'driver_id' => $this->driverId,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'customer_address' => $this->customerAddress,
            'job_type' => $this->jobType,
            'status' => $this->status,
            'priority' => $this->priority,
            'scheduled_date' => $this->scheduledDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'duration_hours' => $this->durationHours,
            'rate_per_unit' => $this->ratePerUnit,
            'total_amount' => $this->totalAmount,
            'notes' => $this->notes,
        ], fn($value) => $value !== null);
    }
}
