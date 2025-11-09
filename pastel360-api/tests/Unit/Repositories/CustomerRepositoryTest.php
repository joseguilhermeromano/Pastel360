<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\CustomerModel;
use App\Repositories\CustomerRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class CustomerRepositoryTest extends TestCase
{
    private $modelMock;
    private CustomerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelMock = Mockery::mock(CustomerModel::class);
        $this->repository = new CustomerRepository($this->modelMock);
    }

    public function test_all_returns_all_customers()
    {
        $collection = new Collection([new \stdClass, new \stdClass]);

        $this->modelMock->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $result = $this->repository->all();

        $this->assertCount(2, $result);
    }

    public function test_find_returns_customer()
    {
        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($this->modelMock);

        $result = $this->repository->find(1);

        $this->assertInstanceOf(CustomerModel::class, $result);
    }

    public function test_create_stores_customer_with_all_fields()
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

        $this->assertInstanceOf(CustomerModel::class, $result);
    }

    public function test_update_modifies_customer()
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

        $this->assertInstanceOf(CustomerModel::class, $result);
    }

    public function test_delete_removes_customer()
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
