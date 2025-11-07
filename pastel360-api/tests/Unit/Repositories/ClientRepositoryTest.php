<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\ClientModel;
use App\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class ClientRepositoryTest extends TestCase
{
    private $modelMock;
    private ClientRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelMock = Mockery::mock(ClientModel::class);
        $this->repository = new ClientRepository($this->modelMock);
    }

    public function test_all_returns_all_clients()
    {
        $collection = new Collection([new \stdClass, new \stdClass]);

        $this->modelMock->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $result = $this->repository->all();

        $this->assertCount(2, $result);
    }

    public function test_find_returns_client()
    {
        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($this->modelMock);

        $result = $this->repository->find(1);

        $this->assertInstanceOf(ClientModel::class, $result);
    }

    public function test_create_stores_client_with_all_fields()
    {
        $data = [
            'name' => 'José Romano',
            'mail' => 'jromano@test.com',
            'phone' => '123456789',
            'birthdate' => '1996-01-01',
            'place' => 'Main Street',
            'number' => '123',
            'zipcode' => '12345678',
            'district' => 'Center',
            'complement' => 'Apt 45'
        ];

        $this->modelMock->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($this->modelMock);

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ClientModel::class, $result);
    }

    public function test_update_modifies_client()
    {
        $data = ['name' => 'Guilherme'];

        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($this->modelMock);

        $this->modelMock->shouldReceive('update')
            ->with($data)
            ->once()
            ->andReturn(true);

        $result = $this->repository->update(1, $data);

        $this->assertInstanceOf(ClientModel::class, $result);
    }

    public function test_delete_removes_client()
    {
        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($this->modelMock);

        $this->modelMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $result = $this->repository->delete(1);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
