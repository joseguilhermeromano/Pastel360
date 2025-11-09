<?php

namespace Database\Factories;

use App\Models\OrderItemModel;
use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemModelFactory extends Factory
{
    protected $model = OrderItemModel::class;

    public function definition()
    {
        $unitValue = $this->faker->randomFloat(2, 5, 100);
        $quantity = $this->faker->numberBetween(1, 5);

        return [
            'product_id' => ProductModel::factory(),
            'quantity' => $quantity,
            'unit_value' => $unitValue,
            'total_value' => $unitValue * $quantity,
        ];
    }
}
