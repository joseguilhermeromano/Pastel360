<?php

namespace Tests\Unit\Controllers;

use Mockery;
use Tests\TestCase;
use App\Models\ProductModel;
use App\Http\Controllers\ProductController;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\JsonResponse;

class ProductControllerTest extends TestCase
{
    private $repositoryMock;
    private $productMock;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->productMock = Mockery::mock(ProductModel::class)->makePartial();
        $this->controller = new ProductController($this->repositoryMock);
    }

    protected function mockRequest(array $data)
    {
        $req = Mockery::mock(ProductRequest::class);
        $req->shouldReceive('validated')->andReturn($data);
        return $req;
    }

    public function test_index_returns_all_products()
    {
        $productArray = ['name' => 'PASTEL CAIPIRA'];
        $this->productMock->shouldReceive('toArray')->andReturn($productArray);

        $this->repositoryMock->shouldReceive('all')
            ->once()
            ->andReturn(collect([$this->productMock, $this->productMock]));

        $response = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertCount(2, $response->getData(true));
    }

    public function test_store_creates_product()
    {
        $data = [
            'name' => 'PASTEL CAIPIRA',
            'description' => 'FRANGO, CATUPIRY E MILHO',
            'price' => 10.00,
            'photo' => 'pastel-caipira.jpg',
            'stock' => 50,
            'enable' => true
        ];

        $this->productMock->shouldReceive('toArray')->andReturn($data);

        $this->repositoryMock->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($this->productMock);

        $request = $this->mockRequest($data);
        $response = $this->controller->store($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($data, $response->getData(true));
    }

    public function test_show_returns_product()
    {
        $attrs = [
            'id' => 1,
            'name' => 'PASTEL CAIPIRA',
            'description' => 'FRANGO, CATUPIRY E MILHO',
            'price' => 10.00,
            'photo' => 'pastel-caipira.jpg',
            'stock' => 50,
            'sku' => 'pastel-caipira-001',
            'enable' => true
        ];

        $this->productMock->shouldReceive('toArray')->andReturn($attrs);

        $this->repositoryMock->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($this->productMock);

        $response = $this->controller->show(1);

        $this->assertEquals($attrs, $response->getData(true));
    }

    public function test_update_modifies_product()
    {
        $updated = [
            'name' => 'PASTEL CAIPIRA COM MUSSARELA',
            'price' => 13.99,
            'stock' => 30
        ];

        $this->productMock->shouldReceive('toArray')->andReturn($updated);

        $this->repositoryMock->shouldReceive('update')
            ->with(1, $updated)
            ->once()
            ->andReturn($this->productMock);

        $request = $this->mockRequest($updated);
        $response = $this->controller->update($request, 1);

        $this->assertEquals($updated, $response->getData(true));
    }

    public function test_destroy_deletes_product()
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
