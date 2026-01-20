<?php

namespace App\Repositories;

use App\Models\Machine;
use App\Repositories\Interfaces\MachineRepositoryInterface;

class MachineRepository implements MachineRepositoryInterface
{
    public function create(array $data): object
    {
        return Machine::create($data);
    }

    public function findById(int $id): ?object
    {
        return Machine::with(['organization'])->find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?object
    {
        return Machine::where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function findByOrganization(int $organizationId, array $filters = []): object
    {
        $query = Machine::where('organization_id', $organizationId);

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['machine_type'])) {
            $query->where('machine_type', $filters['machine_type']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('registration_number', 'like', "%{$filters['search']}%");
            });
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('name', 'asc')->paginate($perPage);
    }

    public function update(int $id, array $data): bool
    {
        $machine = Machine::find($id);
        return $machine ? $machine->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $machine = Machine::find($id);
        return $machine ? $machine->delete() : false;
    }

    public function findActive(int $organizationId): array
    {
        return Machine::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->get()
            ->toArray();
    }
}
