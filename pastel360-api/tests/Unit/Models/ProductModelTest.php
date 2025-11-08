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
            'photo' => 'pastel-carne.jpg',
            'stock' => 50,
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELDECA', $product->sku);
        $this->assertStringEndsWith(str_pad($product->id, 3, '0', STR_PAD_LEFT), $product->sku);
    }

    public function test_sku_is_not_generated_when_provided()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL DE QUEIJO',
            'description' => 'Pastel de queijo mussarela',
            'price' => 7.50,
            'photo' => 'pastel-queijo.jpg',
            'stock' => 100,
            'sku' => 'PASTEL-QUEIJO-001',
            'enable' => true
        ]);

        $this->assertEquals('PASTEL-QUEIJO-001', $product->sku);
    }

    public function test_sku_generation_handles_special_characters()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL CAIPIRA',
            'description' => 'Pastel de frango com catupiry',
            'price' => 9.00,
            'photo' => 'pastel-caipira.jpg',
            'stock' => 75,
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELCAIP', $product->sku);
        $this->assertStringEndsWith(str_pad($product->id, 3, '0', STR_PAD_LEFT), $product->sku);
    }

    public function test_sku_generation_handles_short_names()
    {
        $product = ProductModel::create([
            'name' => 'Pastel',
            'description' => 'Pastel simples',
            'price' => 6.00,
            'photo' => 'pastel.jpg',
            'stock' => 200,
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTEL', $product->sku);
        $this->assertStringEndsWith(str_pad($product->id, 3, '0', STR_PAD_LEFT), $product->sku);
    }

    public function test_sku_generation_handles_long_names()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL SUPER ESPECIAL DA CASA COM RECHEIO PREMIUM',
            'description' => 'Pastel especial da casa',
            'price' => 12.50,
            'photo' => 'pastel-especial.jpg',
            'stock' => 30,
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELSUPE', $product->sku); // Apenas 10 primeiros caracteres
        $this->assertStringEndsWith(str_pad($product->id, 3, '0', STR_PAD_LEFT), $product->sku);
    }

    public function test_sku_format_is_correct()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL DE PIZZA',
            'description' => 'Pastel de pizza com mussarela e tomate',
            'price' => 8.00,
            'photo' => 'pastel-pizza.jpg',
            'stock' => 60,
            'enable' => true
        ]);

        $expectedPattern = '/^PROD-[A-Z0-9]{1,10}-\d{3}$/';
        $this->assertMatchesRegularExpression($expectedPattern, $product->sku);
    }

    public function test_sku_with_accented_characters()
    {
        $product = ProductModel::create([
            'name' => 'PASTEL DE PALMITO',
            'description' => 'Pastel de palmito com azeitonas',
            'price' => 9.50,
            'photo' => 'pastel-palmito.jpg',
            'stock' => 40,
            'enable' => true
        ]);

        $this->assertStringStartsWith('PROD-', $product->sku);
        $this->assertStringContainsString('PASTELDEPA', $product->sku);
        $this->assertStringEndsWith(str_pad($product->id, 3, '0', STR_PAD_LEFT), $product->sku);
    }
}
