<?php

namespace App\DTOs;

class LandDTO
{
    public string $id;
    public string $organizationId;
    public string $ownerId;
    public string $name;
    public float $area; // in acres or hectares
    public string $coordinates; // GeoJSON or similar format
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        string $id,
        string $organizationId,
        string $ownerId,
        string $name,
        float $area,
        string $coordinates,
        string $createdAt,
        string $updatedAt
    ) {
        $this->id = $id;
        $this->organizationId = $organizationId;
        $this->ownerId = $ownerId;
        $this->name = $name;
        $this->area = $area;
        $this->coordinates = $coordinates;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}