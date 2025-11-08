<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\OrderModel;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class OrderRepositoryTest extends TestCase
{
    private $modelMock;
    private OrderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelMock = Mockery::mock(OrderModel::class);
        $this->repository = new OrderRepository($this->modelMock);
    }

    public function test_all_returns_all_orders()
    {
        $collection = new Collection([new \stdClass, new \stdClass]);

        $this->modelMock->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $result = $this->repository->all();

        $this->assertCount(2, $result);
    }

    public function test_find_returns_order()
    {
        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($this->modelMock);

        $result = $this->repository->find(1);

        $this->assertInstanceOf(OrderModel::class, $result);
    }

    public function test_create_stores_order_with_all_fields()
    {
        $data = [
            'product_id' => 1,
            'client_id' => 1,
            'quantity' => 2,
            'unit_value' => 10.00,
            'status' => 'pending',
            'notes' => 'Entregar de manhã'
        ];

        $this->modelMock->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($this->modelMock);

        $result = $this->repository->create($data);

        $this->assertInstanceOf(OrderModel::class, $result);
    }

    public function test_update_modifies_order()
    {
        $data = ['status' => 'approved'];

        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($this->modelMock);

        $this->modelMock->shouldReceive('update')
            ->with($data)
            ->once()
            ->andReturn(true);

        $result = $this->repository->update(1, $data);

        $this->assertInstanceOf(OrderModel::class, $result);
    }

    public function test_delete_removes_order()
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
