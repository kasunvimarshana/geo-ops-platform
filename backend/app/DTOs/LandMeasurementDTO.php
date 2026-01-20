<?php

namespace App\DTOs;

class LandMeasurementDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $customerId,
        public readonly string $fieldName,
        public readonly string $coordinates,
        public readonly float $areaSqm,
        public readonly float $areaAcres,
        public readonly float $areaHectares,
        public readonly ?string $locationAddress,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            customerId: $data['customer_id'],
            fieldName: $data['field_name'],
            coordinates: is_array($data['coordinates']) ? json_encode($data['coordinates']) : $data['coordinates'],
            areaSqm: (float) $data['area_sqm'],
            areaAcres: (float) $data['area_acres'],
            areaHectares: (float) $data['area_hectares'],
            locationAddress: $data['location_address'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'organization_id' => $this->organizationId,
            'customer_id' => $this->customerId,
            'field_name' => $this->fieldName,
            'coordinates' => $this->coordinates,
            'area_sqm' => $this->areaSqm,
            'area_acres' => $this->areaAcres,
            'area_hectares' => $this->areaHectares,
            'location_address' => $this->locationAddress,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'notes' => $this->notes,
        ], fn($value) => $value !== null);
    }

    public function getCoordinatesArray(): array
    {
        return json_decode($this->coordinates, true);
    }
}
