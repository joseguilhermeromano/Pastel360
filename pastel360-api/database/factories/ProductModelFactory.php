<?php

namespace Database\Factories;

use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductModelFactory extends Factory
{
    protected $model = ProductModel::class;

    public function definition()
    {
        $name = $this->faker->word;

        $tempProduct = new ProductModel(['name' => $name]);
        $sku = $tempProduct->sku;

        return [
            'name' => $name,
            'sku' => $sku,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'stock' => $this->faker->numberBetween(1, 100),
            'photo' => $sku . '.jpg',
            'enable' => true,
            'category' => $this->faker->randomElement(['salgado', 'doce', 'especial']),
        ];
    }
}
