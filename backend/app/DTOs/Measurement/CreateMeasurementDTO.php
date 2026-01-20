<?php

namespace App\DTOs\Measurement;

/**
 * Create Measurement DTO
 * 
 * Data Transfer Object for creating a new measurement record.
 */
class CreateMeasurementDTO
{
    public function __construct(
        public readonly int $landId,
        public readonly string $type,
        public readonly array $coordinates,
        public readonly string $measurementStartedAt,
        public readonly string $measurementCompletedAt,
        public readonly ?string $notes = null,
        public readonly ?float $accuracyMeters = null,
        public readonly ?string $deviceId = null,
        public readonly bool $isSynced = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            landId: $data['land_id'],
            type: $data['type'],
            coordinates: $data['coordinates'],
            measurementStartedAt: $data['measurement_started_at'],
            measurementCompletedAt: $data['measurement_completed_at'],
            notes: $data['notes'] ?? null,
            accuracyMeters: $data['accuracy_meters'] ?? null,
            deviceId: $data['device_id'] ?? null,
            isSynced: $data['is_synced'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'land_id' => $this->landId,
            'type' => $this->type,
            'coordinates' => $this->coordinates,
            'measurement_started_at' => $this->measurementStartedAt,
            'measurement_completed_at' => $this->measurementCompletedAt,
            'notes' => $this->notes,
            'accuracy_meters' => $this->accuracyMeters,
            'device_id' => $this->deviceId,
            'is_synced' => $this->isSynced,
        ];
    }
}
