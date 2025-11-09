<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CustomerModel;

class CustomerModelTest extends TestCase
{
    public function test_customer_creation()
    {
        $customer = CustomerModel::make([
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

        $this->assertInstanceOf(CustomerModel::class, $customer);
        $this->assertEquals('John Doe', $customer->name);
        $this->assertEquals('john@test.com', $customer->mail);
        $this->assertEquals('123456789', $customer->phone);
        $this->assertEquals('1990-01-01', $customer->birthdate->format('Y-m-d'));
        $this->assertEquals('Main Street', $customer->place);
        $this->assertEquals('123', $customer->number);
        $this->assertEquals('12345678', $customer->zipcode);
        $this->assertEquals('Central', $customer->district);
        $this->assertEquals('Apt 45', $customer->complement);
    }
}
