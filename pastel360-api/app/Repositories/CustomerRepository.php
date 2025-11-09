<?php

namespace App\Repositories;

use App\Models\CustomerModel;
use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    protected CustomerModel $customer;

    public function __construct(CustomerModel $customer)
    {
        $this->customer = $customer;
    }

    public function all()
    {
        return $this->customer->all();
    }

    public function find(int $id)
    {
        return $this->customer->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->customer->create($data);
    }

    public function update(int $id, array $data)
    {
        $customer = $this->find($id);
        $customer->update($data);
        return $customer;
    }

    public function delete(int $id)
    {
        $customer = $this->find($id);
        return $customer->delete();
    }
}
