<?php

namespace App\DTOs;

class JobDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $customerId,
        public readonly ?int $landMeasurementId,
        public readonly ?int $driverId,
        public readonly ?int $machineId,
        public readonly string $serviceType,
        public readonly string $status,
        public readonly ?string $scheduledDate,
        public readonly ?string $startedAt,
        public readonly ?string $completedAt,
        public readonly ?float $ratePerAcre,
        public readonly ?float $ratePerHectare,
        public readonly ?float $totalAmount,
        public readonly ?string $notes,
        public readonly ?string $locationAddress,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            customerId: $data['customer_id'],
            landMeasurementId: $data['land_measurement_id'] ?? null,
            driverId: $data['driver_id'] ?? null,
            machineId: $data['machine_id'] ?? null,
            serviceType: $data['service_type'],
            status: $data['status'] ?? 'pending',
            scheduledDate: $data['scheduled_date'] ?? null,
            startedAt: $data['started_at'] ?? null,
            completedAt: $data['completed_at'] ?? null,
            ratePerAcre: isset($data['rate_per_acre']) ? (float) $data['rate_per_acre'] : null,
            ratePerHectare: isset($data['rate_per_hectare']) ? (float) $data['rate_per_hectare'] : null,
            totalAmount: isset($data['total_amount']) ? (float) $data['total_amount'] : null,
            notes: $data['notes'] ?? null,
            locationAddress: $data['location_address'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'organization_id' => $this->organizationId,
            'customer_id' => $this->customerId,
            'land_measurement_id' => $this->landMeasurementId,
            'driver_id' => $this->driverId,
            'machine_id' => $this->machineId,
            'service_type' => $this->serviceType,
            'status' => $this->status,
            'scheduled_date' => $this->scheduledDate,
            'started_at' => $this->startedAt,
            'completed_at' => $this->completedAt,
            'rate_per_acre' => $this->ratePerAcre,
            'rate_per_hectare' => $this->ratePerHectare,
            'total_amount' => $this->totalAmount,
            'notes' => $this->notes,
            'location_address' => $this->locationAddress,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ], fn($value) => $value !== null);
    }
}
