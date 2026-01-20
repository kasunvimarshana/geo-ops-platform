<?php

namespace App\Repositories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Collection;

class JobRepository
{
    protected $model;

    public function __construct(Job $job)
    {
        $this->model = $job;
    }

    public function create(array $data): Job
    {
        return $this->model->create($data);
    }

    public function find(int $id): ?Job
    {
        return $this->model->find($id);
    }

    public function update(int $id, array $data): bool
    {
        $job = $this->find($id);
        return $job ? $job->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $job = $this->find($id);
        return $job ? $job->delete() : false;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }
}