<?php

namespace Database\Seeders;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $customers = CustomerModel::all();
        $products = ProductModel::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->error('É necessário ter clientes e produtos antes de criar pedidos!');
            return;
        }

        $orders = [
            [
                'customer_id' => $customers[0]->id,
                'status' => 'delivered',
                'total_amount' => 24.50,
                'notes' => 'Entregar antes das 19h'
            ],
            [
                'customer_id' => $customers[1]->id,
                'status' => 'canceled',
                'total_amount' => 16.00,
                'notes' => 'Sem cebola no pastel de carne'
            ],
            [
                'customer_id' => $customers[2]->id,
                'status' => 'approved',
                'total_amount' => 31.50,
                'notes' => 'Pedido para viagem'
            ],
            [
                'customer_id' => $customers[3]->id,
                'status' => 'pending',
                'total_amount' => 15.00,
                'notes' => null
            ],
            [
                'customer_id' => $customers[4]->id,
                'status' => 'approved',
                'total_amount' => 22.50,
                'notes' => 'Adicionar molho extra'
            ],
            [
                'customer_id' => $customers[5]->id,
                'status' => 'delivered',
                'total_amount' => 18.50,
                'notes' => 'Entregar no portão 2'
            ],
            [
                'customer_id' => $customers[6]->id,
                'status' => 'approved',
                'total_amount' => 27.00,
                'notes' => 'Cliente vai buscar'
            ],
            [
                'customer_id' => $customers[7]->id,
                'status' => 'canceled',
                'total_amount' => 14.00,
                'notes' => 'Cliente cancelou'
            ],
            [
                'customer_id' => $customers[8]->id,
                'status' => 'canceled',
                'total_amount' => 20.50,
                'notes' => 'Com bastante recheio'
            ],
            [
                'customer_id' => $customers[9]->id,
                'status' => 'approved',
                'total_amount' => 25.00,
                'notes' => 'Fritar bem'
            ]
        ];

        foreach ($orders as $orderData) {
            $order = OrderModel::create($orderData);

            $this->createOrderItems($order, $products);

            $order->refreshTotalPrice();
        }

        $this->command->info('10 pedidos criados com sucesso!');
    }

    private function createOrderItems(OrderModel $order, $products)
    {
        $itemsCount = rand(1, 4);

        for ($i = 0; $i < $itemsCount; $i++) {
            $product = $products->random();
            $quantity = rand(1, 3);

            OrderItemModel::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_value' => $product->price,
                'total_value' => $quantity * $product->price
            ]);
        }
    }
}
