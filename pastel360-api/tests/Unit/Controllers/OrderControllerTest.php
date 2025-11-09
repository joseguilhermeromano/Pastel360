<?php

namespace Tests\Unit\Controllers;

use Mockery;
use Tests\TestCase;
use App\Models\OrderModel;
use App\Http\Controllers\OrderController;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\JsonResponse;

class OrderControllerTest extends TestCase
{
    private $repositoryMock;
    private $orderMock;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        $this->orderMock = Mockery::mock(OrderModel::class)->makePartial();
        $this->controller = new OrderController($this->repositoryMock);
    }

    protected function mockRequest(array $data)
    {
        $req = Mockery::mock(OrderRequest::class);
        $req->shouldReceive('validated')->andReturn($data);
        return $req;
    }

    public function test_index_returns_all_orders()
    {
        $orderArray = ['id' => 1, 'status' => 'pending'];
        $this->orderMock->shouldReceive('toArray')->andReturn($orderArray);

        $this->repositoryMock->shouldReceive('all')
            ->once()
            ->andReturn(collect([$this->orderMock, $this->orderMock]));

        $response = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertCount(2, $response->getData(true));
    }

    public function test_store_creates_order()
    {
        $data = [
            'product_id' => 1,
            'customer_id' => 1,
            'quantity' => 2,
            'unit_value' => 10.00,
            'status' => 'pending',
            'notes' => 'Entregar de manhã'
        ];

        $this->orderMock->shouldReceive('toArray')->andReturn($data);

        $this->repositoryMock->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($this->orderMock);

        $request = $this->mockRequest($data);
        $response = $this->controller->store($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($data, $response->getData(true));
    }

    public function test_show_returns_order()
    {
        $attrs = [
            'id' => 1,
            'product_id' => 1,
            'customer_id' => 1,
            'quantity' => 2,
            'unit_value' => 10.00,
            'total_value' => 20.00,
            'status' => 'pending',
            'notes' => 'Entregar de manhã'
        ];

        $this->orderMock->shouldReceive('toArray')->andReturn($attrs);

        $this->repositoryMock->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($this->orderMock);

        $response = $this->controller->show(1);

        $this->assertEquals($attrs, $response->getData(true));
    }

    public function test_update_modifies_order()
    {
        $updated = [
            'status' => 'approved',
            'notes' => 'Entregue com sucesso'
        ];

        $this->orderMock->shouldReceive('toArray')->andReturn($updated);

        $this->repositoryMock->shouldReceive('update')
            ->with(1, $updated)
            ->once()
            ->andReturn($this->orderMock);

        $request = $this->mockRequest($updated);
        $response = $this->controller->update($request, 1);

        $this->assertEquals($updated, $response->getData(true));
    }

    public function test_destroy_deletes_order()
    {
        $this->repositoryMock->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $response = $this->controller->destroy(1);

        $this->assertEquals(204, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
