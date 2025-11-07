<?php

namespace Tests\Unit\Http\Controllers;

use Mockery;
use Tests\TestCase;
use App\Models\ClientModel;
use App\Http\Controllers\ClientController;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\JsonResponse;

class ClientControllerTest extends TestCase
{
    private $repositoryMock;
    private $clientMock;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(ClientRepositoryInterface::class);
        $this->clientMock = Mockery::mock(ClientModel::class)->makePartial();
        $this->controller = new ClientController($this->repositoryMock);
    }

    protected function mockRequest(array $data)
    {
        $req = Mockery::mock(ClientRequest::class);
        $req->shouldReceive('validated')->andReturn($data);
        return $req;
    }

    public function test_index_returns_all_clients()
    {
        $clientArray = ['name' => 'Teste'];
        $this->clientMock->shouldReceive('toArray')->andReturn($clientArray);

        $this->repositoryMock->shouldReceive('all')
            ->once()
            ->andReturn(collect([$this->clientMock, $this->clientMock]));

        $response = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertCount(2, $response->getData(true));
    }

    public function test_store_creates_client()
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

        $this->clientMock->shouldReceive('toArray')->andReturn($data);

        $this->repositoryMock->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($this->clientMock);

        $request = $this->mockRequest($data);
        $response = $this->controller->store($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($data, $response->getData(true));
    }

    public function test_show_returns_client()
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

        $this->clientMock->shouldReceive('toArray')->andReturn($attrs);

        $this->repositoryMock->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($this->clientMock);

        $response = $this->controller->show(1);

        $this->assertEquals($attrs, $response->getData(true));
    }

    public function test_update_modifies_client()
    {
        $updated = [
            'name' => 'José Romano',
            'mail' => 'jromano@test.com'
        ];

        $this->clientMock->shouldReceive('toArray')->andReturn($updated);

        $this->repositoryMock->shouldReceive('update')
            ->with(1, $updated)
            ->once()
            ->andReturn($this->clientMock);

        $request = $this->mockRequest($updated);
        $response = $this->controller->update($request, 1);

        $this->assertEquals($updated, $response->getData(true));
    }

    public function test_destroy_deletes_client()
    {
        $this->repositoryMock->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $response = $this->controller->destroy(1);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
