<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\FieldJobRepositoryInterface;
use App\Models\FieldJob;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FieldJobRepository implements FieldJobRepositoryInterface
{
    public function findById(int $id): ?FieldJob
    {
        return FieldJob::with(['organization', 'landPlot', 'driver', 'creator'])->find($id);
    }

    public function findByOrganization(int $organizationId): Collection
    {
        return FieldJob::organization($organizationId)
            ->with(['landPlot', 'driver'])
            ->orderBy('scheduled_date', 'desc')
            ->get();
    }

    public function findByStatus(int $organizationId, string $status): Collection
    {
        return FieldJob::organization($organizationId)
            ->status($status)
            ->with(['landPlot', 'driver'])
            ->orderBy('scheduled_date', 'desc')
            ->get();
    }

    public function paginateByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return FieldJob::organization($organizationId)
            ->with(['landPlot', 'driver'])
            ->orderBy('scheduled_date', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): FieldJob
    {
        return FieldJob::create($data);
    }

    public function update(int $id, array $data): FieldJob
    {
        $fieldJob = FieldJob::findOrFail($id);
        $fieldJob->update($data);
        return $fieldJob->fresh(['organization', 'landPlot', 'driver', 'creator']);
    }

    public function delete(int $id): bool
    {
        $fieldJob = FieldJob::findOrFail($id);
        return $fieldJob->delete();
    }

    public function updateStatus(int $id, string $status): FieldJob
    {
        $fieldJob = FieldJob::findOrFail($id);
        $fieldJob->update(['status' => $status]);
        return $fieldJob->fresh();
    }
}
