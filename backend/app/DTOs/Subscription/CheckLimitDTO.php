<?php

namespace App\DTOs\Subscription;

class CheckLimitDTO
{
    public function __construct(
        public readonly string $resource,
        public readonly ?int $count = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            resource: $data['resource'],
            count: $data['count'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'resource' => $this->resource,
            'count' => $this->count,
        ];
    }
}
