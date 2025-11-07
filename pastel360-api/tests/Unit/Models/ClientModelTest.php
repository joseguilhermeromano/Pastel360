<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ClientModel;

class ClientModelTest extends TestCase
{
    public function test_client_creation()
    {
        $client = ClientModel::make([
            'name' => 'John Doe',
            'mail' => 'john@test.com',
            'phone' => '123456789',
            'birthdate' => '1990-01-01',
            'place' => 'Main Street',
            'number' => '123',
            'zipcode' => '12345678',
            'district' => 'Central',
            'complement' => 'Apt 45'
        ]);

        $this->assertInstanceOf(ClientModel::class, $client);
        $this->assertEquals('John Doe', $client->name);
        $this->assertEquals('john@test.com', $client->mail);
        $this->assertEquals('123456789', $client->phone);
        $this->assertEquals('1990-01-01', $client->birthdate->format('Y-m-d'));
        $this->assertEquals('Main Street', $client->place);
        $this->assertEquals('123', $client->number);
        $this->assertEquals('12345678', $client->zipcode);
        $this->assertEquals('Central', $client->district);
        $this->assertEquals('Apt 45', $client->complement);
    }
}
