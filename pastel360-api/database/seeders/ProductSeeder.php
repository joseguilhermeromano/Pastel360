<?php

namespace Database\Seeders;

use App\Models\ProductModel;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Pastel de Carne',
                'description' => 'Pastel de carne moída com temperos especiais',
                'price' => 8.50,
                'stock' => 50,
                'enable' => true,
                'category' => 'salgado'
            ],
            [
                'name' => 'Pastel de Queijo',
                'description' => 'Pastel de queijo mussarela derretido',
                'price' => 7.50,
                'stock' => 45,
                'enable' => true,
                'category' => 'salgado'
            ],
            [
                'name' => 'Pastel de Frango',
                'description' => 'Pastel de frango desfiado com catupiry',
                'price' => 8.00,
                'stock' => 35,
                'enable' => true,
                'category' => 'salgado'
            ],
            [
                'name' => 'Pastel de Pizza',
                'description' => 'Pastel com molho de tomate, queijo e presunto',
                'price' => 8.50,
                'stock' => 40,
                'enable' => true,
                'category' => 'salgado'
            ],
            [
                'name' => 'Pastel de Calabresa',
                'description' => 'Pastel de calabresa com cebola',
                'price' => 8.00,
                'stock' => 30,
                'enable' => true,
                'category' => 'salgado'
            ],
            [
                'name' => 'Pastel de Palmito',
                'description' => 'Pastel de palmito com queijo',
                'price' => 9.00,
                'stock' => 25,
                'enable' => true,
                'category' => 'salgado'
            ],
            [
                'name' => 'Pastel de Banana',
                'description' => 'Pastel doce de banana com canela',
                'price' => 6.50,
                'stock' => 20,
                'enable' => true,
                'category' => 'doce'
            ],
            [
                'name' => 'Pastel de Romeu e Julieta',
                'description' => 'Pastel doce de goiabada com queijo',
                'price' => 7.00,
                'stock' => 15,
                'enable' => true,
                'category' => 'doce'
            ],
            [
                'name' => 'Pastel de Chocolate',
                'description' => 'Pastel doce de chocolate ao leite',
                'price' => 6.50,
                'stock' => 18,
                'enable' => true,
                'category' => 'doce'
            ],
            [
                'name' => 'Pastel Especial da Casa',
                'description' => 'Pastel especial com carne, queijo, tomate e azeitonas',
                'price' => 10.00,
                'stock' => 10,
                'enable' => true,
                'category' => 'especial'
            ]
        ];

        foreach ($products as $productData) {
            $tempProduct = new ProductModel($productData);
            $sku = $tempProduct->sku;

            ProductModel::create(array_merge($productData, [
                'sku' => $sku,
                'photo' => $sku . '.jpg'
            ]));
        }

        $this->command->info('10 produtos (pastéis) criados com sucesso!');
    }
}
