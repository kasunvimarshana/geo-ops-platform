<?php

namespace App\DTOs;

/**
 * Land Measurement Data Transfer Object
 * 
 * Immutable data structure for land measurement data
 * Following DTO pattern for Clean Architecture
 */
class LandMeasurementDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $measurementType,
        public readonly array $polygon,
        public readonly ?string $locationName,
        public readonly ?string $customerName,
        public readonly ?string $customerPhone,
        public readonly ?string $measuredAt,
        public readonly ?string $offlineId
    ) {
        $this->validate();
    }

    /**
     * Create DTO from array (typically from request)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            measurementType: $data['measurement_type'] ?? 'walk-around',
            polygon: $data['polygon'] ?? [],
            locationName: $data['location_name'] ?? null,
            customerName: $data['customer_name'] ?? null,
            customerPhone: $data['customer_phone'] ?? null,
            measuredAt: $data['measured_at'] ?? null,
            offlineId: $data['offline_id'] ?? null
        );
    }

    /**
     * Validate DTO data
     */
    private function validate(): void
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException('Land name is required');
        }

        if (!in_array($this->measurementType, ['walk-around', 'point-based'])) {
            throw new \InvalidArgumentException('Invalid measurement type');
        }

        if (count($this->polygon) < 3) {
            throw new \InvalidArgumentException('Polygon must have at least 3 points');
        }

        foreach ($this->polygon as $point) {
            if (!isset($point['latitude']) || !isset($point['longitude'])) {
                throw new \InvalidArgumentException('Each polygon point must have latitude and longitude');
            }
        }
    }
}
