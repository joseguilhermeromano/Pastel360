<?php

namespace Database\Factories;

use App\Models\OrderModel;
use App\Models\CustomerModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderModelFactory extends Factory
{
    protected $model = OrderModel::class;

    public function definition()
    {
        return [
            'customer_id' => CustomerModel::factory(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'delivered', 'canceled']),
            'total_amount' => 0,
            'notes' => $this->faker->optional(0.3)->sentence(6),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (OrderModel $order) {
            if ($order->items()->count() === 0) {
                \App\Models\OrderItemModel::factory()
                    ->count($this->faker->numberBetween(1, 4))
                    ->create(['order_id' => $order->id]);

                $order->refreshTotalPrice();
            }
        });
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
            ];
        });
    }

    public function inPreparation()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'in_preparation',
            ];
        });
    }

    public function ready()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'ready',
            ];
        });
    }

    public function delivered()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'delivered',
            ];
        });
    }

    public function canceled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'canceled',
            ];
        });
    }

    public function withNotes(string $notes)
    {
        return $this->state(function (array $attributes) use ($notes) {
            return [
                'notes' => $notes,
            ];
        });
    }

    public function withoutNotes()
    {
        return $this->state(function (array $attributes) {
            return [
                'notes' => null,
            ];
        });
    }
}
