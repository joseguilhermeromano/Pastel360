<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class ProductRequestTest extends TestCase
{
    public function test_product_store_validation_passes_with_valid_data()
    {
        $data = [
            'name' => 'Pastel de Teste 1',
            'description' => 'Pastel de teste com ingredientes especiais',
            'price' => 8.50,
            'stock' => 50,
            'enable' => true,
        ];

        $request = new ProductRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->fails());
    }

    public function test_product_store_validation_passes_with_valid_data_and_image()
    {
        $file = UploadedFile::fake()->create('pastel.jpg', 1000, 'image/jpeg');

        $data = [
            'name' => 'Pastel de Teste 2',
            'description' => 'Pastel de teste com ingredientes especiais',
            'price' => 8.50,
            'photo' => $file,
            'stock' => 50,
            'enable' => true,
        ];

        $request = new ProductRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->fails());
    }

    public function test_product_store_validation_fails_with_invalid_data()
    {
        $invalidData = [
            'name' => '',
            'price' => -10,
            'stock' => -5,
        ];

        $request = new ProductRequest();
        $rules = $request->rules();

        $validator = Validator::make($invalidData, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }

    public function test_product_store_validation_fails_with_invalid_image_type()
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $data = [
            'name' => 'Pastel de Teste 3',
            'description' => 'Pastel de teste',
            'price' => 8.50,
            'photo' => $invalidFile,
            'stock' => 50,
            'enable' => true
        ];

        $request = new ProductRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('photo', $validator->errors()->toArray());
    }

    public function test_product_store_validation_fails_with_image_too_large()
    {
        $largeFile = UploadedFile::fake()->create('large.jpg', 3000, 'image/jpeg'); // 3MB

        $data = [
            'name' => 'Pastel de Teste 4',
            'description' => 'Pastel de teste',
            'price' => 8.50,
            'photo' => $largeFile,
            'stock' => 50,
            'enable' => true
        ];

        $request = new ProductRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('photo', $validator->errors()->toArray());
    }

    public function test_product_store_validation_passes_without_image()
    {
        $data = [
            'name' => 'Pastel de Teste 5',
            'description' => 'Pastel de teste sem imagem',
            'price' => 8.50,
            'stock' => 50,
            'enable' => true
        ];

        $request = new ProductRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->fails());
    }

    public function test_product_update_validation_passes_with_valid_data()
    {
        $data = [
            'name' => 'Pastel Atualizado',
            'price' => 9.50,
            'stock' => 25
        ];

        $request = new ProductRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->fails());
    }

    public function test_product_update_validation_passes_with_new_image()
    {
        $file = UploadedFile::fake()->create('new-pastel.jpg', 1000, 'image/jpeg');

        $data = [
            'name' => 'Pastel Atualizado',
            'price' => 9.00,
            'photo' => $file,
            'stock' => 30
        ];

        $request = new ProductRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->fails());
    }

    public function test_messages_returns_correct_validation_messages()
    {
        $request = new ProductRequest();

        $messages = $request->messages();

        $expectedMessages = [
            'name.required' => 'O nome do produto é obrigatório.',
            'price.required' => 'O preço é obrigatório.',
            'price.min' => 'O preço deve ser maior que zero.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A imagem deve ser JPEG, PNG, JPG ou GIF.',
            'photo.max' => 'A imagem não pode ser maior que 2MB.',
            'stock.required' => 'O estoque é obrigatório.',
        ];

        $this->assertEquals($expectedMessages, $messages);
    }

    public function test_prepare_for_validation_sets_photo_to_null_when_no_file_is_provided()
    {
        $request = ProductRequest::create('/test', 'POST', [
            'name' => 'Produto Teste',
            'price' => 10.50,
            'stock' => 5,
            'photo' => 'existing-value.jpg'
        ]);

        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $this->assertNull($request->get('photo'));
    }

    public function test_prepare_for_validation_preserves_photo_when_file_is_provided()
    {
        $uploadedFile = UploadedFile::fake()->create('product.jpg', 1024, 'image/jpeg');

        $request = ProductRequest::create('/test', 'POST', [
            'name' => 'Produto Teste',
            'price' => 10.50,
            'stock' => 5,
        ], [], ['photo' => $uploadedFile]);

        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $this->assertNotNull($request->file('photo'));
        $this->assertEquals('product.jpg', $request->file('photo')->getClientOriginalName());
    }

    public function test_validation_behavior_after_prepare_for_validation()
    {
        $request1 = ProductRequest::create('/test', 'POST', [
            'name' => 'Produto Teste',
            'price' => 10.50,
            'stock' => 5,
            'photo' => 'some-value.jpg'
        ]);

        $reflection = new \ReflectionClass($request1);
        $method = $reflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request1);

        $this->assertNull($request1->get('photo'), 'Photo should be null when no file is provided');

        $uploadedFile = UploadedFile::fake()->create('product.jpg', 1024, 'image/jpeg');
        $request2 = ProductRequest::create('/test', 'POST', [
            'name' => 'Produto Teste',
            'price' => 10.50,
            'stock' => 5,
        ], [], ['photo' => $uploadedFile]);

        $method->invoke($request2);

        $this->assertNotNull($request2->file('photo'), 'Photo file should be preserved when provided');
    }

    public function test_messages_structure_and_content()
    {
        $request = new ProductRequest();
        $messages = $request->messages();

        $this->assertIsArray($messages);

        $expectedKeys = [
            'name.required',
            'price.required',
            'price.min',
            'photo.image',
            'photo.mimes',
            'photo.max',
            'stock.required'
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $messages, "Mensagem para {$key} não encontrada");
        }

        $this->assertEquals('O nome do produto é obrigatório.', $messages['name.required']);
        $this->assertEquals('O preço é obrigatório.', $messages['price.required']);
        $this->assertEquals('O preço deve ser maior que zero.', $messages['price.min']);
        $this->assertEquals('O arquivo deve ser uma imagem.', $messages['photo.image']);
        $this->assertEquals('A imagem deve ser JPEG, PNG, JPG ou GIF.', $messages['photo.mimes']);
        $this->assertEquals('A imagem não pode ser maior que 2MB.', $messages['photo.max']);
        $this->assertEquals('O estoque é obrigatório.', $messages['stock.required']);
    }

    public function test_photo_rules_are_included_for_post_method()
    {
        $request = ProductRequest::create('/products', 'POST');

        $rules = $request->rules();

        $this->assertArrayHasKey('photo', $rules);
        $this->assertEquals('nullable|image|mimes:jpeg,png,jpg,gif|max:2048', $rules['photo']);
    }

    public function test_photo_rules_are_included_for_patch_method()
    {
        $request = ProductRequest::create('/products/1', 'PATCH');

        $rules = $request->rules();

        $this->assertArrayHasKey('photo', $rules);
        $this->assertEquals('nullable|image|mimes:jpeg,png,jpg,gif|max:2048', $rules['photo']);
    }

    public function test_product_request_authorize_allows_access()
    {
        $request = new ProductRequest();
        $this->assertTrue($request->authorize());
    }
}
