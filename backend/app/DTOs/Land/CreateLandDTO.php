<?php

namespace App\DTOs\Land;

use Illuminate\Http\Request;

/**
 * Create Land DTO
 * 
 * Data Transfer Object for creating a new land record.
 * Ensures type safety and decoupling from HTTP layer.
 */
class CreateLandDTO
{
    public function __construct(
        public readonly string $name,
        public readonly array $coordinates,
        public readonly ?string $description = null,
        public readonly ?int $ownerUserId = null,
        public readonly ?string $locationAddress = null,
        public readonly ?string $locationDistrict = null,
        public readonly ?string $locationProvince = null,
    ) {}

    /**
     * Create DTO from HTTP Request
     * 
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            coordinates: $request->input('coordinates'),
            description: $request->input('description'),
            ownerUserId: $request->input('owner_user_id'),
            locationAddress: $request->input('location_address'),
            locationDistrict: $request->input('location_district'),
            locationProvince: $request->input('location_province'),
        );
    }

    /**
     * Create DTO from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            coordinates: $data['coordinates'],
            description: $data['description'] ?? null,
            ownerUserId: $data['owner_user_id'] ?? null,
            locationAddress: $data['location_address'] ?? null,
            locationDistrict: $data['location_district'] ?? null,
            locationProvince: $data['location_province'] ?? null,
        );
    }

    /**
     * Convert DTO to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'coordinates' => $this->coordinates,
            'description' => $this->description,
            'owner_user_id' => $this->ownerUserId,
            'location_address' => $this->locationAddress,
            'location_district' => $this->locationDistrict,
            'location_province' => $this->locationProvince,
        ];
    }
}
