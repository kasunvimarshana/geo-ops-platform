<?php

namespace App\DTOs;

class JobDTO
{
    public int $id;
    public int $landId;
    public int $driverId;
    public string $status;
    public string $description;
    public float $areaMeasured;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        int $id,
        int $landId,
        int $driverId,
        string $status,
        string $description,
        float $areaMeasured,
        string $createdAt,
        string $updatedAt
    ) {
        $this->id = $id;
        $this->landId = $landId;
        $this->driverId = $driverId;
        $this->status = $status;
        $this->description = $description;
        $this->areaMeasured = $areaMeasured;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}