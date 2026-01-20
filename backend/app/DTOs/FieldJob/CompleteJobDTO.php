<?php

namespace App\DTOs\FieldJob;

use Illuminate\Http\Request;

/**
 * Complete Job DTO
 * 
 * Data Transfer Object for completing a field job.
 */
class CompleteJobDTO
{
    public function __construct(
        public readonly ?float $actualAmount = null,
        public readonly ?float $distanceKm = null,
        public readonly ?string $completionNotes = null,
        public readonly ?array $attachments = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            actualAmount: $request->input('actual_amount'),
            distanceKm: $request->input('distance_km'),
            completionNotes: $request->input('completion_notes'),
            attachments: $request->input('attachments'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            actualAmount: $data['actual_amount'] ?? null,
            distanceKm: $data['distance_km'] ?? null,
            completionNotes: $data['completion_notes'] ?? null,
            attachments: $data['attachments'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'actual_amount' => $this->actualAmount,
            'distance_km' => $this->distanceKm,
            'completion_notes' => $this->completionNotes,
            'attachments' => $this->attachments,
        ], fn($value) => $value !== null);
    }
}
