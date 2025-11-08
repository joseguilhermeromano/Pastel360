<?php

namespace App\Repositories;

use App\Models\OrderModel;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(private OrderModel $model) {}

    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $order = $this->model->findOrFail($id);
        $order->update($data);
        return $order;
    }

    public function delete(int $id)
    {
        $order = $this->model->findOrFail($id);
        return $order->delete();
    }
}
