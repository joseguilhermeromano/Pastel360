<?php

namespace Tests\Unit\Controllers;

use Mockery;
use Tests\TestCase;
use App\Models\CustomerModel;
use App\Http\Controllers\CustomerController;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\JsonResponse;

class CustomerControllerTest extends TestCase
{
    private $repositoryMock;
    private $customerMock;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(CustomerRepositoryInterface::class);
        $this->customerMock = Mockery::mock(CustomerModel::class)->makePartial();
        $this->controller = new CustomerController($this->repositoryMock);
    }

    protected function mockRequest(array $data)
    {
        $req = Mockery::mock(CustomerRequest::class);
        $req->shouldReceive('validated')->andReturn($data);
        return $req;
    }

    public function test_index_returns_all_customers()
    {
        $customerArray = ['name' => 'Teste'];
        $this->customerMock->shouldReceive('toArray')->andReturn($customerArray);

        $this->repositoryMock->shouldReceive('all')
            ->once()
            ->andReturn(collect([$this->customerMock, $this->customerMock]));

        $response = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertCount(2, $response->getData(true));
    }

    public function test_store_creates_customer()
    {
        $data = [
            'name' => 'José Romano',
            'mail' => 'jromano@test.com',
            'phone' => '123456789',
            'birthdate' => '1996-01-01',
            'place' => 'Rua X',
            'number' => '111',
            'zipcode' => '01234567',
            'district' => 'Centro',
            'complement' => 'Apto 10'
        ];

        $this->customerMock->shouldReceive('toArray')->andReturn($data);

        $this->repositoryMock->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($this->customerMock);

        $request = $this->mockRequest($data);
        $response = $this->controller->store($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($data, $response->getData(true));
    }

    public function test_show_returns_customer()
    {
        $attrs = [
            'id' => 1,
            'name' => 'José Romano',
            'mail' => 'jromano@test.com',
            'phone' => '99999999',
            'birthdate' => '1990-01-01',
            'place' => 'Rua A',
            'number' => '123',
            'zipcode' => '00000000',
            'district' => 'Bairro',
            'complement' => null
        ];

        $this->customerMock->shouldReceive('toArray')->andReturn($attrs);

        $this->repositoryMock->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($this->customerMock);

        $response = $this->controller->show(1);

        $this->assertEquals($attrs, $response->getData(true));
    }

    public function test_update_modifies_customer()
    {
        $updated = [
            'name' => 'José Romano',
            'mail' => 'jromano@test.com'
        ];

        $this->customerMock->shouldReceive('toArray')->andReturn($updated);

        $this->repositoryMock->shouldReceive('update')
            ->with(1, $updated)
            ->once()
            ->andReturn($this->customerMock);

        $request = $this->mockRequest($updated);
        $response = $this->controller->update($request, 1);

        $this->assertEquals($updated, $response->getData(true));
    }

    public function test_destroy_deletes_customer()
    {
        $this->repositoryMock->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $response = $this->controller->destroy(1);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
