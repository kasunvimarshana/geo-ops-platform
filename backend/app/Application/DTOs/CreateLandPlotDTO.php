<?php

declare(strict_types=1);

namespace App\Application\DTOs;

class CreateLandPlotDTO
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $userId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly float $areaAcres,
        public readonly float $areaHectares,
        public readonly float $areaSquareMeters,
        public readonly ?float $perimeterMeters,
        public readonly array $coordinates,
        public readonly float $centerLatitude,
        public readonly float $centerLongitude,
        public readonly ?string $location,
        public readonly string $measurementMethod,
        public readonly ?float $accuracyMeters,
        public readonly string $measuredAt,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'],
            userId: $data['user_id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            areaAcres: $data['area_acres'],
            areaHectares: $data['area_hectares'],
            areaSquareMeters: $data['area_square_meters'],
            perimeterMeters: $data['perimeter_meters'] ?? null,
            coordinates: $data['coordinates'],
            centerLatitude: $data['center_latitude'],
            centerLongitude: $data['center_longitude'],
            location: $data['location'] ?? null,
            measurementMethod: $data['measurement_method'],
            accuracyMeters: $data['accuracy_meters'] ?? null,
            measuredAt: $data['measured_at'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'organization_id' => $this->organizationId,
            'user_id' => $this->userId,
            'name' => $this->name,
            'description' => $this->description,
            'area_acres' => $this->areaAcres,
            'area_hectares' => $this->areaHectares,
            'area_square_meters' => $this->areaSquareMeters,
            'perimeter_meters' => $this->perimeterMeters,
            'coordinates' => $this->coordinates,
            'center_latitude' => $this->centerLatitude,
            'center_longitude' => $this->centerLongitude,
            'location' => $this->location,
            'measurement_method' => $this->measurementMethod,
            'accuracy_meters' => $this->accuracyMeters,
            'measured_at' => $this->measuredAt,
            'notes' => $this->notes,
        ];
    }
}
