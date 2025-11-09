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
                'photo' => 'pastel-carne.jpg',
                'stock' => 50,
                'enable' => true
            ],
            [
                'name' => 'Pastel de Queijo',
                'description' => 'Pastel de queijo mussarela derretido',
                'price' => 7.50,
                'photo' => 'pastel-queijo.jpg',
                'stock' => 45,
                'enable' => true
            ],
            [
                'name' => 'Pastel de Frango',
                'description' => 'Pastel de frango desfiado com catupiry',
                'price' => 8.00,
                'photo' => 'pastel-frango.jpg',
                'stock' => 35,
                'enable' => true
            ],
            [
                'name' => 'Pastel de Pizza',
                'description' => 'Pastel com molho de tomate, queijo e presunto',
                'price' => 8.50,
                'photo' => 'pastel-pizza.jpg',
                'stock' => 40,
                'enable' => true
            ],
            [
                'name' => 'Pastel de Calabresa',
                'description' => 'Pastel de calabresa com cebola',
                'price' => 8.00,
                'photo' => 'pastel-calabresa.jpg',
                'stock' => 30,
                'enable' => true
            ],
            [
                'name' => 'Pastel de Palmito',
                'description' => 'Pastel de palmito com queijo',
                'price' => 9.00,
                'photo' => 'pastel-palmito.jpg',
                'stock' => 25,
                'enable' => true
            ],
            [
                'name' => 'Pastel de Banana',
                'description' => 'Pastel doce de banana com canela',
                'price' => 6.50,
                'photo' => 'pastel-banana.jpg',
                'stock' => 20,
                'enable' => true
            ],
            [
                'name' => 'Pastel de Romeu e Julieta',
                'description' => 'Pastel doce de goiabada com queijo',
                'price' => 7.00,
                'photo' => 'pastel-romeu-julieta.jpg',
                'stock' => 15,
                'enable' => true
            ],
            [
                'name' => 'Pastel de Chocolate',
                'description' => 'Pastel doce de chocolate ao leite',
                'price' => 6.50,
                'photo' => 'pastel-chocolate.jpg',
                'stock' => 18,
                'enable' => true
            ],
            [
                'name' => 'Pastel Especial da Casa',
                'description' => 'Pastel especial com carne, queijo, tomate e azeitonas',
                'price' => 10.00,
                'photo' => 'pastel-especial.jpg',
                'stock' => 10,
                'enable' => true
            ]
        ];

        foreach ($products as $product) {
            ProductModel::create($product);
        }

        $this->command->info('10 produtos (pastéis) criados com sucesso!');
    }
}
