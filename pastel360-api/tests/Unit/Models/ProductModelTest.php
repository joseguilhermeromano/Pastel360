<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ProductModel;

class ProductModelTest extends TestCase
{
    public function test_product_creation()
    {
        $product = ProductModel::make([
            'name' => 'PASTEL PIZZA TEST',
            'description' => 'INGREDIENTES: MUSSARELA, TOMATE, PRESUNTO E ORÉGANO',
            'price' => 10.99,
            'stock' => 10,
            'sku' => 'PASTEL-PIZZA-001',
            'enable' => true
        ]);

        $this->assertInstanceOf(ProductModel::class, $product);
        $this->assertEquals('PASTEL PIZZA TEST', $product->name);
        $this->assertEquals('INGREDIENTES: MUSSARELA, TOMATE, PRESUNTO E ORÉGANO', $product->description);
        $this->assertEquals(10, $product->stock);
        $this->assertEquals('PASTEL-PIZZA-001', $product->sku);
        $this->assertEquals(10.99, $product->price);
        $this->assertTrue($product->enable);
    }
}
