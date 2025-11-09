<?php

namespace Database\Seeders;

use App\Models\CustomerModel;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            [
                'name' => 'João Silva',
                'mail' => 'joao.silva@email.com',
                'phone' => '(11) 99999-1111',
                'birthdate' => '1985-05-15',
                'place' => 'Rua das Flores',
                'number' => '123',
                'zipcode' => '01234-567',
                'district' => 'Centro',
                'complement' => 'Apto 101',
            ],
            [
                'name' => 'Maria Santos',
                'mail' => 'maria.santos@email.com',
                'phone' => '(11) 99999-2222',
                'birthdate' => '1990-08-22',
                'place' => 'Avenida Paulista',
                'number' => '1500',
                'zipcode' => '01310-100',
                'district' => 'Bela Vista',
                'complement' => 'Sala 205',
            ],
            [
                'name' => 'Pedro Oliveira',
                'mail' => 'pedro.oliveira@email.com',
                'phone' => '(11) 99999-3333',
                'birthdate' => '1988-12-10',
                'place' => 'Rua Augusta',
                'number' => '500',
                'zipcode' => '01305-000',
                'district' => 'Consolação',
                'complement' => 'Loja 10',
            ],
            [
                'name' => 'Ana Costa',
                'mail' => 'ana.costa@email.com',
                'phone' => '(11) 99999-4444',
                'birthdate' => '1992-03-30',
                'place' => 'Alameda Santos',
                'number' => '2000',
                'zipcode' => '01418-200',
                'district' => 'Jardins',
                'complement' => 'Conjunto 304',
            ],
            [
                'name' => 'Carlos Pereira',
                'mail' => 'carlos.pereira@email.com',
                'phone' => '(11) 99999-5555',
                'birthdate' => '1980-11-05',
                'place' => 'Rua da Consolação',
                'number' => '3001',
                'zipcode' => '01301-000',
                'district' => 'Consolação',
                'complement' => 'Bloco B',
            ],
            [
                'name' => 'Fernanda Lima',
                'mail' => 'fernanda.lima@email.com',
                'phone' => '(11) 99999-6666',
                'birthdate' => '1995-07-18',
                'place' => 'Rua Haddock Lobo',
                'number' => '595',
                'zipcode' => '01414-000',
                'district' => 'Cerqueira César',
                'complement' => null,
            ],
            [
                'name' => 'Ricardo Almeida',
                'mail' => 'ricardo.almeida@email.com',
                'phone' => '(11) 99999-7777',
                'birthdate' => '1987-01-25',
                'place' => 'Avenida Brigadeiro Faria Lima',
                'number' => '3477',
                'zipcode' => '04538-133',
                'district' => 'Itaim Bibi',
                'complement' => 'Andar 15',
            ],
            [
                'name' => 'Juliana Rodrigues',
                'mail' => 'juliana.rodrigues@email.com',
                'phone' => '(11) 99999-8888',
                'birthdate' => '1993-09-12',
                'place' => 'Rua Oscar Freire',
                'number' => '800',
                'zipcode' => '01426-000',
                'district' => 'Jardins',
                'complement' => 'Apto 502',
            ],
            [
                'name' => 'Roberto Nunes',
                'mail' => 'roberto.nunes@email.com',
                'phone' => '(11) 99999-9999',
                'birthdate' => '1983-06-08',
                'place' => 'Rua Bela Cintra',
                'number' => '934',
                'zipcode' => '01415-000',
                'district' => 'Consolação',
                'complement' => 'Casa 2',
            ],
            [
                'name' => 'Amanda Souza',
                'mail' => 'amanda.souza@email.com',
                'phone' => '(11) 99999-0000',
                'birthdate' => '1991-04-14',
                'place' => 'Alameda Jaú',
                'number' => '1502',
                'zipcode' => '01420-001',
                'district' => 'Jardim Paulista',
                'complement' => 'Apto 801',
            ]
        ];

        foreach ($customers as $customer) {
            CustomerModel::create($customer);
        }

        $this->command->info('10 clientes criados com sucesso!');
    }
}
