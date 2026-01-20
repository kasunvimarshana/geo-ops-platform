<?php

namespace App\Repositories;

use App\Models\Land;
use Illuminate\Database\Eloquent\Collection;

class LandRepository
{
    protected $model;

    public function __construct(Land $land)
    {
        $this->model = $land;
    }

    public function all(): Collection
    {
        return $this->model::all();
    }

    public function find($id): ?Land
    {
        return $this->model::find($id);
    }

    public function create(array $data): Land
    {
        return $this->model::create($data);
    }

    public function update($id, array $data): bool
    {
        $land = $this->find($id);
        return $land ? $land->update($data) : false;
    }

    public function delete($id): bool
    {
        $land = $this->find($id);
        return $land ? $land->delete() : false;
    }
}