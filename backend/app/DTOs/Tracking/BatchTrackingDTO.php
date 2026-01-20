<?php

namespace App\DTOs\Tracking;

use Illuminate\Http\Request;

/**
 * Batch Tracking DTO
 * 
 * Data Transfer Object for batch creating tracking log entries.
 */
class BatchTrackingDTO
{
    public function __construct(
        public readonly array $trackingLogs,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $logs = [];
        foreach ($request->input('tracking_logs', []) as $logData) {
            $logs[] = CreateTrackingLogDTO::fromArray($logData);
        }

        return new self(trackingLogs: $logs);
    }

    public static function fromArray(array $data): self
    {
        $logs = [];
        foreach ($data['tracking_logs'] ?? [] as $logData) {
            $logs[] = CreateTrackingLogDTO::fromArray($logData);
        }

        return new self(trackingLogs: $logs);
    }

    public function toArray(): array
    {
        return [
            'tracking_logs' => array_map(fn($dto) => $dto->toArray(), $this->trackingLogs),
        ];
    }
}
