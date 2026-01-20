<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): object
    {
        return User::create($data);
    }

    public function findById(int $id): ?object
    {
        return User::with(['organization', 'role'])->find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?object
    {
        return User::with(['role'])
            ->where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function findByOrganization(int $organizationId, array $filters = []): object
    {
        $query = User::with(['role'])
            ->where('organization_id', $organizationId);

        if (isset($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%")
                    ->orWhere('phone', 'like', "%{$filters['search']}%");
            });
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('name', 'asc')->paginate($perPage);
    }

    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        return $user ? $user->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        return $user ? $user->delete() : false;
    }

    public function findByEmail(string $email): ?object
    {
        return User::with(['organization', 'role'])->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?object
    {
        return User::with(['organization', 'role'])->where('phone', $phone)->first();
    }

    public function findDrivers(int $organizationId): array
    {
        return User::where('organization_id', $organizationId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'driver');
            })
            ->where('is_active', true)
            ->get()
            ->toArray();
    }
}
