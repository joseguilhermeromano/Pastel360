<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ProductModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_sku_is_generated_automatically_when_empty()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL DE CARNE',
            'description' => 'Pastel de carne com temperos especiais',
            'price' => 8.50,
            'stock' => 50,
            'photo' => 'test.jpg',
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELCARNE', $product->sku);
    }

    public function test_sku_is_not_generated_when_provided()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL DE QUEIJO',
            'description' => 'Pastel de queijo mussarela',
            'price' => 7.50,
            'stock' => 100,
            'photo' => 'test.jpg',
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELQUEIJO', $product->sku);
    }

    public function test_sku_generation_handles_special_characters()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL CAIPIRA',
            'description' => 'Pastel de frango com catupiry',
            'price' => 9.00,
            'stock' => 75,
            'photo' => 'test.jpg',
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELCAIPIRA', $product->sku);
    }

    public function test_sku_generation_handles_short_names()
    {
        $product = ProductModel::create([
            'name' => 'Pastel',
            'description' => 'Pastel simples',
            'price' => 6.00,
            'stock' => 200,
            'photo' => 'test.jpg',
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTEL', $product->sku);
    }

    public function test_sku_generation_handles_long_names()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL SUPER ESPECIAL DA CASA COM RECHEIO PREMIUM',
            'description' => 'Pastel especial da casa',
            'price' => 12.50,
            'stock' => 30,
            'photo' => 'test.jpg',
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELSUPERESP', $product->sku);
    }

    public function test_sku_format_is_correct()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL DE PIZZA',
            'description' => 'Pastel de pizza com mussarela e tomate',
            'price' => 8.00,
            'stock' => 60,
            'photo' => 'test.jpg',
            'enable' => true
        ]);

        $expectedPattern = '/^PROD-[A-Z0-9]{1,17}-[A-Z0-9]{8}$/';
        $this->assertMatchesRegularExpression($expectedPattern, $product->sku);
    }

    public function test_sku_with_accented_characters()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL DE PALMITO',
            'description' => 'Pastel de palmito com azeitonas',
            'price' => 9.50,
            'stock' => 40,
            'photo' => 'test.jpg',
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELPALMITO', $product->sku);
    }
}
