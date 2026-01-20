<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Repositories\LandPlotRepositoryInterface;
use App\Models\LandPlot;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LandPlotService
{
    public function __construct(
        private readonly LandPlotRepositoryInterface $repository
    ) {}

    public function getById(int $id): ?LandPlot
    {
        return $this->repository->findById($id);
    }

    public function getAllByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateByOrganization($organizationId, $perPage);
    }

    public function create(array $data): LandPlot
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): LandPlot
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function calculateArea(array $coordinates): array
    {
        $earthRadius = 6371000; // meters
        $area = 0;
        $perimeter = 0;

        $numPoints = count($coordinates);
        
        for ($i = 0; $i < $numPoints; $i++) {
            $j = ($i + 1) % $numPoints;
            
            $lat1 = deg2rad($coordinates[$i]['latitude']);
            $lon1 = deg2rad($coordinates[$i]['longitude']);
            $lat2 = deg2rad($coordinates[$j]['latitude']);
            $lon2 = deg2rad($coordinates[$j]['longitude']);
            
            $area += ($lon2 - $lon1) * (2 + sin($lat1) + sin($lat2));
            
            $dLat = $lat2 - $lat1;
            $dLon = $lon2 - $lon1;
            $a = sin($dLat/2) * sin($dLat/2) + cos($lat1) * cos($lat2) * sin($dLon/2) * sin($dLon/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            $perimeter += $earthRadius * $c;
        }
        
        $areaSquareMeters = abs($area * $earthRadius * $earthRadius / 2);
        $areaHectares = $areaSquareMeters / 10000;
        $areaAcres = $areaSquareMeters / 4046.86;

        return [
            'area_square_meters' => round($areaSquareMeters, 2),
            'area_hectares' => round($areaHectares, 4),
            'area_acres' => round($areaAcres, 4),
            'perimeter_meters' => round($perimeter, 2),
        ];
    }

    public function calculateCenter(array $coordinates): array
    {
        $latSum = 0;
        $lonSum = 0;
        $count = count($coordinates);

        foreach ($coordinates as $coord) {
            $latSum += $coord['latitude'];
            $lonSum += $coord['longitude'];
        }

        return [
            'center_latitude' => $latSum / $count,
            'center_longitude' => $lonSum / $count,
        ];
    }
}
