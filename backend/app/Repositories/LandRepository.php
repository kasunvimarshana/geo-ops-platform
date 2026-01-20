<?php

namespace App\Repositories;

use App\Models\Land;
use App\Repositories\Interfaces\LandRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LandRepository implements LandRepositoryInterface
{
    public function create(array $data): object
    {
        return Land::create($data);
    }

    public function findById(int $id): ?object
    {
        return Land::with(['measurer', 'measurementPoints', 'organization'])->find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?object
    {
        return Land::with(['measurer', 'measurementPoints'])
            ->where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function findByOrganization(int $organizationId, array $filters = []): object
    {
        $query = Land::with(['measurer', 'measurementPoints'])
            ->where('organization_id', $organizationId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('customer_name', 'like', "%{$filters['search']}%")
                    ->orWhere('location_name', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['measured_by'])) {
            $query->where('measured_by', $filters['measured_by']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('measured_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('measured_at', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findNearby(float $latitude, float $longitude, int $radiusMeters, int $organizationId): array
    {
        $point = "POINT($longitude $latitude)";
        
        return Land::where('organization_id', $organizationId)
            ->select('*')
            ->selectRaw("ST_Distance_Sphere(polygon, ST_GeomFromText('$point', 4326)) as distance")
            ->whereRaw("ST_Distance_Sphere(polygon, ST_GeomFromText('$point', 4326)) <= ?", [$radiusMeters])
            ->orderBy('distance')
            ->get()
            ->toArray();
    }

    public function update(int $id, array $data): bool
    {
        $land = Land::find($id);
        return $land ? $land->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $land = Land::find($id);
        return $land ? $land->delete() : false;
    }

    public function findByOfflineId(string $offlineId, int $organizationId): ?object
    {
        return Land::where('offline_id', $offlineId)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function getPendingSync(int $organizationId): array
    {
        return Land::where('organization_id', $organizationId)
            ->where('sync_status', 'pending')
            ->get()
            ->toArray();
    }
}
