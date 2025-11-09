<?php

namespace Database\Factories;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderModelFactory extends Factory
{
    protected $model = OrderModel::class;

    public function definition()
    {
        return [
            'product_id' => ProductModel::factory(),
            'customer_id' => CustomerModel::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_value' => $this->faker->randomFloat(2, 10, 100),
            'total_value' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['unit_value'];
            },
            'status' => $this->faker->randomElement(['pending', 'approved', 'canceled', 'delivered']),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
