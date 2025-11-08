<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\ProductModel;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class ProductRepositoryTest extends TestCase
{
    private $modelMock;
    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelMock = Mockery::mock(ProductModel::class);
        $this->repository = new ProductRepository($this->modelMock);
    }

    public function test_all_returns_all_products()
    {
        $collection = new Collection([new \stdClass, new \stdClass]);

        $this->modelMock->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $result = $this->repository->all();

        $this->assertCount(2, $result);
    }

    public function test_find_returns_product()
    {
        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($this->modelMock);

        $result = $this->repository->find(1);

        $this->assertInstanceOf(ProductModel::class, $result);
    }

    public function test_create_stores_product_with_all_fields()
    {
        $data = [
            'name' => 'PASTEL CAIPIRA',
            'description' => 'FRANGO, CATUPIRY E MILHO',
            'price' => 10.00,
            'photo' => 'pastel-caipira.jpg',
            'stock' => 50,
            'enable' => true
        ];

        $this->modelMock->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($this->modelMock);

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ProductModel::class, $result);
    }

    public function test_update_modifies_product()
    {
        $data = ['name' => 'PASTE CAIPIRA'];

        $this->modelMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($this->modelMock);

        $this->modelMock->shouldReceive('update')
            ->with($data)
            ->once()
            ->andReturn(true);

        $result = $this->repository->update(1, $data);

        $this->assertInstanceOf(ProductModel::class, $result);
    }

    public function test_delete_removes_product()
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
