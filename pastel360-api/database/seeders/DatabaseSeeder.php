<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CustomerSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);

        $this->command->info('Todos os seeders executados com sucesso!');
        $this->command->info('Resumo:');
        $this->command->info('10 Clientes');
        $this->command->info('10 Produtos (Pastéis)');
        $this->command->info('10 Pedidos com itens');
    }
}
