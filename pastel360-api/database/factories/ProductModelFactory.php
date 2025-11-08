<?php

namespace Database\Factories;

use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductModelFactory extends Factory
{
    protected $model = ProductModel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'photo' => $this->faker->word . '.jpg',
            'stock' => $this->faker->numberBetween(1, 100),
            'enable' => true,
        ];
    }
}
