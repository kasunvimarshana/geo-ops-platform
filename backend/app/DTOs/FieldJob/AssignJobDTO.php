<?php

namespace App\DTOs\FieldJob;

use Illuminate\Http\Request;

/**
 * Assign Job DTO
 * 
 * Data Transfer Object for assigning a job to a driver.
 */
class AssignJobDTO
{
    public function __construct(
        public readonly int $driverId,
        public readonly ?string $notes = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            driverId: $request->input('driver_id'),
            notes: $request->input('notes'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            driverId: $data['driver_id'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'driver_id' => $this->driverId,
            'notes' => $this->notes,
        ];
    }
}
