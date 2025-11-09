<?php

namespace App\Repositories;

use App\Models\OrderModel;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(private OrderModel $model) {}

    public function all()
    {
        return $this->model->with(['customer', 'items.product'])->get();
    }

    public function find(int $id)
    {
        return $this->model->with(['customer', 'items.product'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            $order = $this->model->create($data);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            $order->refreshTotalPrice();

            return $order->load(['customer', 'items.product']);
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $order = $this->model->with('items')->findOrFail($id);

            $items = $data['items'] ?? [];
            unset($data['items']);

            $order->update($data);

            if (!empty($items)) {
                $this->syncOrderItems($order, $items);
            }

            $order->refreshTotalPrice();

            return $order->load(['customer', 'items.product']);
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $order = $this->model->findOrFail($id);
            return $order->delete();
        });
    }

    private function syncOrderItems(OrderModel $order, array $items): void
    {
        $existingItems = [];
        $newItems = [];

        foreach ($items as $item) {
            if (isset($item['id'])) {
                $existingItems[$item['id']] = $item;
            } else {
                $newItems[] = $item;
            }
        }

        foreach ($order->items as $existingItem) {
            if (isset($existingItems[$existingItem->id])) {
                $existingItem->update($existingItems[$existingItem->id]);
            } else {
                $existingItem->delete();
            }
        }

        foreach ($newItems as $newItem) {
            $order->items()->create($newItem);
        }
    }
}
