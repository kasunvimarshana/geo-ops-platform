<?php

namespace App\Services;

use App\Models\Land;
use App\DTOs\LandDTO;
use App\Repositories\LandRepository;

class LandMeasurementService
{
    protected $landRepository;

    public function __construct(LandRepository $landRepository)
    {
        $this->landRepository = $landRepository;
    }

    public function createLandMeasurement(LandDTO $landDTO): Land
    {
        $land = new Land();
        $land->name = $landDTO->name;
        $land->area = $landDTO->area;
        $land->coordinates = $landDTO->coordinates;
        $land->organization_id = $landDTO->organizationId;

        return $this->landRepository->save($land);
    }

    public function updateLandMeasurement(int $id, LandDTO $landDTO): Land
    {
        $land = $this->landRepository->find($id);
        if (!$land) {
            throw new \Exception('Land measurement not found.');
        }

        $land->name = $landDTO->name;
        $land->area = $landDTO->area;
        $land->coordinates = $landDTO->coordinates;

        return $this->landRepository->save($land);
    }

    public function getLandMeasurement(int $id): Land
    {
        $land = $this->landRepository->find($id);
        if (!$land) {
            throw new \Exception('Land measurement not found.');
        }

        return $land;
    }

    public function deleteLandMeasurement(int $id): bool
    {
        $land = $this->landRepository->find($id);
        if (!$land) {
            throw new \Exception('Land measurement not found.');
        }

        return $this->landRepository->delete($land);
    }
}