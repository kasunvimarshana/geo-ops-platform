<?php

namespace App\DTOs\Tracking;

use Illuminate\Http\Request;

/**
 * Create Tracking Log DTO
 * 
 * Data Transfer Object for creating a single tracking log entry.
 */
class CreateTrackingLogDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly ?int $jobId,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?float $accuracyMeters = null,
        public readonly ?float $altitudeMeters = null,
        public readonly ?float $speedMps = null,
        public readonly ?float $headingDegrees = null,
        public readonly ?string $recordedAt = null,
        public readonly ?string $deviceId = null,
        public readonly ?string $platform = null,
        public readonly ?array $metadata = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            userId: $request->input('user_id'),
            jobId: $request->input('job_id'),
            latitude: (float) $request->input('latitude'),
            longitude: (float) $request->input('longitude'),
            accuracyMeters: $request->filled('accuracy_meters') ? (float) $request->input('accuracy_meters') : null,
            altitudeMeters: $request->filled('altitude_meters') ? (float) $request->input('altitude_meters') : null,
            speedMps: $request->filled('speed_mps') ? (float) $request->input('speed_mps') : null,
            headingDegrees: $request->filled('heading_degrees') ? (float) $request->input('heading_degrees') : null,
            recordedAt: $request->input('recorded_at'),
            deviceId: $request->input('device_id'),
            platform: $request->input('platform'),
            metadata: $request->input('metadata'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            jobId: $data['job_id'] ?? null,
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            accuracyMeters: isset($data['accuracy_meters']) ? (float) $data['accuracy_meters'] : null,
            altitudeMeters: isset($data['altitude_meters']) ? (float) $data['altitude_meters'] : null,
            speedMps: isset($data['speed_mps']) ? (float) $data['speed_mps'] : null,
            headingDegrees: isset($data['heading_degrees']) ? (float) $data['heading_degrees'] : null,
            recordedAt: $data['recorded_at'] ?? null,
            deviceId: $data['device_id'] ?? null,
            platform: $data['platform'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'job_id' => $this->jobId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'accuracy_meters' => $this->accuracyMeters,
            'altitude_meters' => $this->altitudeMeters,
            'speed_mps' => $this->speedMps,
            'heading_degrees' => $this->headingDegrees,
            'recorded_at' => $this->recordedAt,
            'device_id' => $this->deviceId,
            'platform' => $this->platform,
            'metadata' => $this->metadata,
        ];
    }
}
