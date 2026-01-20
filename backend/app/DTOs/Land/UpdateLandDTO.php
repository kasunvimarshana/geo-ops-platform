<?php

namespace App\DTOs\Land;

use Illuminate\Http\Request;

/**
 * Update Land DTO
 * 
 * Data Transfer Object for updating an existing land record.
 * All fields are optional as updates can be partial.
 */
class UpdateLandDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?array $coordinates = null,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
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
            status: $request->input('status'),
            locationAddress: $request->input('location_address'),
            locationDistrict: $request->input('location_district'),
            locationProvince: $request->input('location_province'),
        );
    }

    /**
     * Check if any field has a value
     * 
     * @return bool
     */
    public function hasAnyField(): bool
    {
        return $this->name !== null
            || $this->coordinates !== null
            || $this->description !== null
            || $this->status !== null
            || $this->locationAddress !== null
            || $this->locationDistrict !== null
            || $this->locationProvince !== null;
    }

    /**
     * Convert DTO to array (only non-null values)
     * 
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->coordinates !== null) {
            $data['coordinates'] = $this->coordinates;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        if ($this->locationAddress !== null) {
            $data['location_address'] = $this->locationAddress;
        }

        if ($this->locationDistrict !== null) {
            $data['location_district'] = $this->locationDistrict;
        }

        if ($this->locationProvince !== null) {
            $data['location_province'] = $this->locationProvince;
        }

        return $data;
    }
}
