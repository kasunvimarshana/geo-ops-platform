<?php

namespace App\DTOs\Measurement;

/**
 * Update Measurement DTO
 * 
 * Data Transfer Object for updating an existing measurement record.
 */
class UpdateMeasurementDTO
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?array $coordinates = null,
        public readonly ?string $notes = null,
        public readonly ?bool $isSynced = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] ?? null,
            coordinates: $data['coordinates'] ?? null,
            notes: $data['notes'] ?? null,
            isSynced: $data['is_synced'] ?? null,
        );
    }

    public function toArray(): array
    {
        $result = [];
        
        if ($this->type !== null) {
            $result['type'] = $this->type;
        }
        
        if ($this->coordinates !== null) {
            $result['coordinates'] = $this->coordinates;
        }
        
        if ($this->notes !== null) {
            $result['notes'] = $this->notes;
        }
        
        if ($this->isSynced !== null) {
            $result['is_synced'] = $this->isSynced;
        }
        
        return $result;
    }
}
