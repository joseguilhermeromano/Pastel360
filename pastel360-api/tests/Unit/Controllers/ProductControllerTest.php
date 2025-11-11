<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\ProductController;
use App\Http\Requests\ProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Models\ProductModel;
use Mockery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ReflectionClass;

class ProductControllerTest extends TestCase
{
    private $repositoryMock;
    private $requestMock;
    private ProductController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->requestMock = Mockery::mock(ProductRequest::class);
        $this->controller = new ProductController($this->repositoryMock);

        Storage::fake('products');
        Storage::shouldReceive('disk')->with('products')->andReturnSelf();
        Storage::shouldReceive('disk')->with('public')->andReturnSelf();
        Storage::shouldReceive('exists')->andReturn(true);
        Storage::shouldReceive('delete')->andReturn(true);
        Storage::shouldReceive('putFileAs')->andReturn('new-photo.jpg');
        Storage::shouldReceive('storeAs')->andReturn(true);
    }

    public function test_index_returns_all_products()
    {
        $productMock1 = $this->createProductMock(['id' => 1, 'name' => 'Product 1']);
        $productMock2 = $this->createProductMock(['id' => 2, 'name' => 'Product 2']);

        $products = new Collection([$productMock1, $productMock2]);

        $this->repositoryMock->shouldReceive('all')
            ->once()
            ->andReturn($products);

        $result = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_show_returns_specific_product()
    {
        $productId = 1;
        $productMock = $this->createProductMock(['id' => 1, 'name' => 'Test Product']);

        $this->repositoryMock->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn($productMock);

        $result = $this->controller->show($productId);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_show_returns_404_when_product_not_found()
    {
        $productId = 999;

        $this->repositoryMock->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn(null);

        $response = $this->controller->show($productId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Product not found"}', $response->getContent());
    }

    public function test_get_image_returns_image_file()
    {
        $filename = 'product-image.jpg';

        $tempDir = storage_path('app/public/products/');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempFile = $tempDir . $filename;
        file_put_contents($tempFile, 'test image content');

        $result = $this->controller->getImage($filename);

        $this->assertInstanceOf(BinaryFileResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function test_get_image_returns_404_when_file_not_found()
    {
        $filename = 'non-existent-image.jpg';

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->controller->getImage($filename);
    }

    public function test_store_creates_product()
    {
        $validatedData = [
            'name' => 'Pastel Teste 1',
            'price' => 8.50,
            'stock' => 50,
        ];

        $this->requestMock->shouldReceive('validated')
            ->andReturn($validatedData);

        $this->requestMock->shouldReceive('hasFile')
            ->with('photo')
            ->andReturn(false);

        $productMock = $this->createProductMock(['id' => 1, 'name' => 'Pastel Teste 1']);
        $productMock->shouldReceive('toJson')
            ->andReturn(json_encode(['id' => 1, 'name' => 'Pastel Teste 1']));

        $this->repositoryMock->shouldReceive('create')
            ->with($validatedData)
            ->andReturn($productMock);

        $result = $this->controller->store($this->requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(201, $result->getStatusCode());
    }

    public function test_store_creates_product_with_image()
    {
        $validatedData = [
            'name' => 'Pastel Teste 3',
            'price' => 8.50,
            'stock' => 50,
        ];

        $this->requestMock->shouldReceive('validated')
            ->andReturn($validatedData);

        $this->requestMock->shouldReceive('hasFile')
            ->with('photo')
            ->andReturn(true);

        $fileMock = Mockery::mock(\Illuminate\Http\UploadedFile::class);
        $fileMock->shouldReceive('getClientOriginalExtension')
            ->andReturn('jpg');
        $fileMock->shouldReceive('storeAs')
            ->with('products', Mockery::pattern('/^TEMP-SKU.jpg$/'), 'public')
            ->andReturn('products/PROD-PASTELTESTE3-ABC123.jpg');

        $this->requestMock->shouldReceive('file')
            ->with('photo')
            ->andReturn($fileMock);

        $productMock = $this->createProductMock([
            'id' => 2,
            'name' => 'Pastel Teste 3',
            'sku' => 'PROD-PASTELTESTE3-ABC123'
        ]);
        $productMock->shouldReceive('toJson')
            ->andReturn(json_encode(['id' => 2, 'name' => 'Pastel Teste 3']));

        $this->repositoryMock->shouldReceive('create')
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'Pastel Teste 3'
                    && isset($data['photo'])
                    && preg_match('/^TEMP-SKU.jpg$/', $data['photo']);
            }))
            ->andReturn($productMock);

        $result = $this->controller->store($this->requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(201, $result->getStatusCode());
    }

    public function test_update_modifies_product()
    {
        $productId = 1;
        $validatedData = [
            'name' => 'Pastel Atualizado',
            'price' => 9.50,
            'stock' => 25,
        ];

        $this->requestMock->shouldReceive('validated')
            ->andReturn($validatedData);

        $this->requestMock->shouldReceive('hasFile')
            ->with('photo')
            ->andReturn(false);

        $productMock = $this->createProductMock(['id' => 1, 'name' => 'Pastel Atualizado']);
        $productMock->shouldReceive('getAttribute')
            ->with('photo')
            ->andReturn(null);
        $productMock->shouldReceive('toJson')
            ->andReturn(json_encode(['id' => 1, 'name' => 'Pastel Atualizado']));

        $this->repositoryMock->shouldReceive('find')
            ->with($productId)
            ->andReturn($productMock);

        $this->repositoryMock->shouldReceive('update')
            ->with($productId, $validatedData)
            ->andReturn($productMock);

        $result = $this->controller->update($this->requestMock, $productId);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_update_modifies_product_with_image()
    {
        $productId = 2;
        $validatedData = [
            'name' => 'Pastel Atualizado com Imagem',
            'price' => 10.50,
            'stock' => 30,
        ];

        $this->requestMock->shouldReceive('validated')
            ->andReturn($validatedData);

        $this->requestMock->shouldReceive('hasFile')
            ->with('photo')
            ->andReturn(true);

        $fileMock = Mockery::mock(\Illuminate\Http\UploadedFile::class);
        $fileMock->shouldReceive('getClientOriginalExtension')
            ->andReturn('png');
        $fileMock->shouldReceive('storeAs')
            ->with('products', Mockery::pattern('/^TEMP-SKU.png$/'), 'public')
            ->andReturn('products/PROD-PASTELATUALIZADOC-DEF456.png');

        $this->requestMock->shouldReceive('file')
            ->with('photo')
            ->andReturn($fileMock);

        $existingProductMock = $this->createProductMock([
            'id' => 2,
            'name' => 'Pastel Atualizado com Imagem',
            'sku' => 'PROD-PASTELATUALIZADOC-DEF456',
            'photo' => 'old-image.jpg'
        ]);

        $updatedProductMock = $this->createProductMock([
            'id' => 2,
            'name' => 'Pastel Atualizado com Imagem',
            'sku' => 'PROD-PASTELATUALIZADOC-DEF456'
        ]);
        $updatedProductMock->shouldReceive('toJson')
            ->andReturn(json_encode(['id' => 2, 'name' => 'Pastel Atualizado com Imagem']));

        $this->repositoryMock->shouldReceive('find')
            ->with($productId)
            ->andReturn($existingProductMock);

        $this->repositoryMock->shouldReceive('update')
            ->with($productId, Mockery::on(function ($data) {
                return isset($data['photo'])
                    && preg_match('/^TEMP-SKU.png$/', $data['photo']);
            }))
            ->andReturn($updatedProductMock);

        $result = $this->controller->update($this->requestMock, $productId);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_destroy_deletes_product_and_photo()
    {
        $productId = 1;
        $photoFilename = 'product-photo.jpg';

        $productMock = $this->createProductMock(['id' => 1, 'photo' => $photoFilename]);

        $this->repositoryMock->shouldReceive('find')
            ->with($productId)
            ->andReturn($productMock);

        $this->repositoryMock->shouldReceive('delete')
            ->with($productId)
            ->andReturn(true);

        $result = $this->controller->destroy($productId);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(204, $result->getStatusCode());
    }

    public function test_destroy_deletes_product_without_photo()
    {
        $productId = 2;

        $productMock = $this->createProductMock(['id' => 2, 'photo' => null]);

        $this->repositoryMock->shouldReceive('find')
            ->with($productId)
            ->andReturn($productMock);

        $this->repositoryMock->shouldReceive('delete')
            ->with($productId)
            ->andReturn(true);

        $result = $this->controller->destroy($productId);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(204, $result->getStatusCode());
    }

    public function test_destroy_returns_404_when_product_not_found()
    {
        $productId = 999;

        $this->repositoryMock->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn(null);

        $response = $this->controller->destroy($productId);


        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Product not found"}', $response->getContent());
    }

    public function test_delete_photo_successfully_deletes_existing_photo()
    {
        $filename = 'test-photo.jpg';

        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('deletePhoto');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, $filename);

        $this->assertNull($result);
    }

    public function test_delete_photo_handles_non_existent_file()
    {
        $filename = 'non-existent-photo.jpg';

        Storage::shouldReceive('disk')
            ->with('products')
            ->andReturnSelf();
        Storage::shouldReceive('exists')
            ->with($filename)
            ->andReturn(false);

        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('deletePhoto');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, $filename);

        $this->assertNull($result);
    }

    public function test_upload_photo_creates_filename_correctly()
    {
        $validatedData = [
            'name' => 'Pastel Teste',
            'price' => 8.50,
            'stock' => 50,
        ];

        $fileMock = Mockery::mock(\Illuminate\Http\UploadedFile::class);
        $fileMock->shouldReceive('getClientOriginalExtension')
            ->andReturn('jpg');
        $fileMock->shouldReceive('storeAs')
            ->with('products', Mockery::pattern('/^TEMP-SKU.jpg$/'), 'public')
            ->andReturn('products/TEMP-SKU.jpg');

        $this->requestMock->shouldReceive('hasFile')
            ->with('photo')
            ->andReturn(true);
        $this->requestMock->shouldReceive('file')
            ->with('photo')
            ->andReturn($fileMock);

        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('uploadPhoto');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, $this->requestMock, $validatedData);

        $this->assertStringContainsString('.jpg', $result);
        $this->assertMatchesRegularExpression('/^TEMP-SKU.jpg$/', $result);
    }


    public function test_update_replaces_existing_photo_and_deletes_old_one()
    {
        $productId = 1;
        $oldPhotoName = 'old-photo.jpg';
        $sku = 'TEMP-SKU';

        $existingProduct = new ProductModel();
        $existingProduct->id = $productId;
        $existingProduct->name = 'Produto Teste';
        $existingProduct->photo = $oldPhotoName;
        $existingProduct->sku = $sku;
        $existingProduct->exists = true;

        $newPhoto = Mockery::mock(UploadedFile::class);
        $newPhoto->shouldReceive('getClientOriginalExtension')->andReturn('jpg');
        $newPhoto->shouldReceive('storeAs')
            ->with('products', Mockery::pattern('/^TEMP-SKU.jpg$/'), 'public')
            ->andReturn('products/TEMP-SKU.jpg');

        $requestMock = Mockery::mock(ProductRequest::class);
        $requestMock->shouldReceive('validated')->andReturn([
            'name' => 'Produto Atualizado',
        ]);
        $requestMock->shouldReceive('hasFile')
            ->with('photo')
            ->andReturn(true);
        $requestMock->shouldReceive('file')
            ->with('photo')
            ->andReturn($newPhoto);

        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();
        Storage::shouldReceive('exists')
            ->with($oldPhotoName)
            ->andReturn(true);
        Storage::shouldReceive('delete')
            ->with($oldPhotoName)
            ->andReturn(true);

        $this->repositoryMock->shouldReceive('find')
            ->once()
            ->with($productId)
            ->andReturn($existingProduct);

        $this->repositoryMock->shouldReceive('update')
            ->once()
            ->with($productId, Mockery::on(function ($data) {
                return isset($data['photo'])
                    && preg_match('/^TEMP-SKU.jpg$/', $data['photo']);
            }))
            ->andReturn($existingProduct);

        $response = $this->controller->update($requestMock, $productId);

        $this->assertEquals(200, $response->status());
    }


    private function createProductMock(array $data = []): Mockery\MockInterface
    {
        $mock = Mockery::mock(ProductModel::class);

        $mock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($data) {
                return $data[$key] ?? null;
            });

        $mock->shouldReceive('setAttribute')
            ->andReturnSelf();

        $mock->shouldReceive('toArray')->andReturn($data);
        $mock->shouldReceive('jsonSerialize')->andReturn($data);
        $mock->shouldReceive('toJson')->andReturn(json_encode($data));

        foreach ($data as $key => $value) {
            $mock->{$key} = $value;
        }

        return $mock;
    }


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
