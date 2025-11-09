<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation()
    {
        $product = ProductModel::factory()->create();
        $customer = CustomerModel::factory()->create();

        $order = OrderModel::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'quantity' => 2,
            'unit_value' => 10.50,
            'total_value' => 21.00,
            'status' => 'pending'
        ]);

        $this->assertInstanceOf(OrderModel::class, $order);
        $this->assertEquals(21.00, $order->total_value);
    }

    public function test_total_value_calculated_automatically_on_create()
    {
        $product = ProductModel::factory()->create();
        $customer = CustomerModel::factory()->create();

        $order = OrderModel::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'quantity' => 3,
            'unit_value' => 15.50,
            'status' => 'pending'
        ]);

        $expectedTotal = 3 * 15.50;
        $this->assertEquals($expectedTotal, $order->total_value);
    }

    public function test_total_value_not_calculated_when_provided()
    {
        $product = ProductModel::factory()->create();
        $customer = CustomerModel::factory()->create();

        $order = OrderModel::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'quantity' => 2,
            'unit_value' => 10.00,
            'total_value' => 25.00,
            'status' => 'pending'
        ]);

        $this->assertEquals(25.00, $order->total_value);
    }

    public function test_total_value_recalculated_on_quantity_update()
    {
        $product = ProductModel::factory()->create();
        $customer = CustomerModel::factory()->create();

        $order = OrderModel::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'quantity' => 2,
            'unit_value' => 10.00,
            'status' => 'pending'
        ]);

        $order->update(['quantity' => 5]);

        $expectedTotal = 5 * 10.00;
        $this->assertEquals($expectedTotal, $order->total_value);
    }

    public function test_total_value_recalculated_on_unit_value_update()
    {
        $product = ProductModel::factory()->create();
        $customer = CustomerModel::factory()->create();

        $order = OrderModel::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'quantity' => 2,
            'unit_value' => 10.00,
            'status' => 'pending'
        ]);

        $order->update(['unit_value' => 15.00]);

        $expectedTotal = 2 * 15.00;
        $this->assertEquals($expectedTotal, $order->total_value);
    }

    public function test_total_value_not_recalculated_on_other_field_update()
    {
        $product = ProductModel::factory()->create();
        $customer = CustomerModel::factory()->create();

        $order = OrderModel::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'quantity' => 2,
            'unit_value' => 10.00,
            'status' => 'pending'
        ]);

        $originalTotal = $order->total_value;
        $order->update(['status' => 'approved']);

        $this->assertEquals($originalTotal, $order->total_value);
    }

    public function test_order_belongs_to_customer()
    {
        $order = OrderModel::make(['customer_id' => 1]);
        $relation = $order->customer();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('customer_id', $relation->getForeignKeyName());
    }

    public function test_order_belongs_to_product()
    {
        $order = OrderModel::make(['product_id' => 1]);
        $relation = $order->product();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
    }
}
