<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
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

    public function test_all_returns_all_orders_with_relations()
    {
        $collection = new Collection([
            Mockery::mock(OrderModel::class),
            Mockery::mock(OrderModel::class)
        ]);

        $this->modelMock->shouldReceive('with')
            ->with(['customer', 'items.product'])
            ->once()
            ->andReturn($this->modelMock);

        $this->modelMock->shouldReceive('get')
            ->once()
            ->andReturn($collection);

        $result = $this->repository->all();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_find_returns_order_with_relations()
    {
        $orderMock = Mockery::mock(OrderModel::class);

        $this->modelMock->shouldReceive('with')
            ->with(['customer', 'items.product'])
            ->once()
            ->andReturn($this->modelMock);

        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($orderMock);

        $result = $this->repository->find(1);

        $this->assertInstanceOf(OrderModel::class, $result);
    }

    public function test_create_stores_order_with_items()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $orderData = [
            'customer_id' => 1,
            'status' => 'pending',
            'notes' => 'Entregar de manhã',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_value' => 8.50
                ],
                [
                    'product_id' => 2,
                    'quantity' => 1,
                    'unit_value' => 7.50
                ]
            ]
        ];

        $orderMock = Mockery::mock(OrderModel::class);
        $relationMock = Mockery::mock(HasMany::class);

        $this->modelMock->shouldReceive('create')
            ->with([
                'customer_id' => 1,
                'status' => 'pending',
                'notes' => 'Entregar de manhã'
            ])
            ->once()
            ->andReturn($orderMock);

        $orderMock->shouldReceive('items')
            ->twice()
            ->andReturn($relationMock);

        $relationMock->shouldReceive('create')
            ->times(2)
            ->andReturn(Mockery::mock(OrderItemModel::class));

        $orderMock->shouldReceive('refreshTotalPrice')
            ->once();

        $orderMock->shouldReceive('load')
            ->with(['customer', 'items.product'])
            ->once()
            ->andReturn($orderMock);

        $result = $this->repository->create($orderData);

        $this->assertInstanceOf(OrderModel::class, $result);
    }

    public function test_update_modifies_order_and_items()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $updateData = [
            'status' => 'approved',
            'notes' => 'Pedido aprovado',
            'items' => [
                [
                    'id' => 1,
                    'product_id' => 1,
                    'quantity' => 3,
                    'unit_value' => 8.50
                ],
                [
                    'product_id' => 3,
                    'quantity' => 2,
                    'unit_value' => 9.00
                ]
            ]
        ];

        $orderMock = Mockery::mock(OrderModel::class);
        $relationMock = Mockery::mock(HasMany::class);

        $existingItem1 = Mockery::mock(OrderItemModel::class);
        $existingItem1->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
        $existingItem1->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $existingItem2 = Mockery::mock(OrderItemModel::class);
        $existingItem2->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(2);
        $existingItem2->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $itemsCollection = new Collection([$existingItem1, $existingItem2]);

        $this->modelMock->shouldReceive('with')
            ->with('items')
            ->once()
            ->andReturn($this->modelMock);

        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($orderMock);

        $orderMock->shouldReceive('update')
            ->with([
                'status' => 'approved',
                'notes' => 'Pedido aprovado'
            ])
            ->once();

        $orderMock->shouldReceive('getAttribute')
            ->with('items')
            ->once()
            ->andReturn($itemsCollection);

        $orderMock->shouldReceive('items')
            ->once()
            ->andReturn($relationMock);
        $relationMock->shouldReceive('create')
            ->once()
            ->andReturn(Mockery::mock(OrderItemModel::class));

        $orderMock->shouldReceive('refreshTotalPrice')
            ->once();

        $orderMock->shouldReceive('load')
            ->with(['customer', 'items.product'])
            ->once()
            ->andReturn($orderMock);

        $result = $this->repository->update(1, $updateData);

        $this->assertInstanceOf(OrderModel::class, $result);
    }

    public function test_delete_removes_order_with_transaction()
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $orderMock = Mockery::mock(OrderModel::class);

        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($orderMock);

        $orderMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $result = $this->repository->delete(1);

        $this->assertTrue($result);
    }

    public function test_sync_order_items_method()
    {
        $this->test_update_modifies_order_and_items();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
