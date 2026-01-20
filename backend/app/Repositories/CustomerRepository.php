<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository
{
    protected $model;

    public function __construct(Customer $customer)
    {
        $this->model = $customer;
    }

    public function all(): Collection
    {
        return $this->model::all();
    }

    public function find($id): ?Customer
    {
        return $this->model::find($id);
    }

    public function create(array $data): Customer
    {
        return $this->model::create($data);
    }

    public function update($id, array $data): bool
    {
        $customer = $this->find($id);
        return $customer ? $customer->update($data) : false;
    }

    public function delete($id): bool
    {
        $customer = $this->find($id);
        return $customer ? $customer->delete() : false;
    }
}