<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation()
    {
        $customer = CustomerModel::factory()->create();

        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'total_amount' => 0
        ]);

        $this->assertInstanceOf(OrderModel::class, $order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(0, $order->total_amount);
    }

    public function test_order_with_items_creation()
    {
        $customer = CustomerModel::factory()->create();
        $product1 = ProductModel::factory()->create(['price' => 8.50]);
        $product2 = ProductModel::factory()->create(['price' => 7.50]);

        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending'
        ]);

        $item1 = OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'unit_value' => 8.50
        ]);

        $item2 = OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'unit_value' => 7.50
        ]);

        $order->refreshTotalPrice();

        $expectedTotal = (2 * 8.50) + (1 * 7.50);
        $this->assertEquals($expectedTotal, $order->total_amount);
        $this->assertCount(2, $order->items);
    }

    public function test_total_amount_calculated_from_items()
    {
        $customer = CustomerModel::factory()->create();
        $product = ProductModel::factory()->create(['price' => 10.00]);

        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending'
        ]);

        OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_value' => 10.00
        ]);

        $order->refreshTotalPrice();

        $expectedTotal = 3 * 10.00;
        $this->assertEquals($expectedTotal, $order->total_amount);
    }

    public function test_order_belongs_to_customer()
    {
        $order = OrderModel::make(['customer_id' => 1]);
        $relation = $order->customer();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('customer_id', $relation->getForeignKeyName());
    }

    public function test_order_has_many_items()
    {
        $order = OrderModel::make();
        $relation = $order->items();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals('order_id', $relation->getForeignKeyName());
    }

    public function test_refresh_total_price_method()
    {
        $customer = CustomerModel::factory()->create();
        $product1 = ProductModel::factory()->create(['price' => 8.50]);
        $product2 = ProductModel::factory()->create(['price' => 9.00]);

        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'total_amount' => 0
        ]);

        OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'unit_value' => 8.50
        ]);

        OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'unit_value' => 9.00
        ]);

        $order->refreshTotalPrice();
        $order->refresh();

        $expectedTotal = (2 * 8.50) + (1 * 9.00);
        $this->assertEquals($expectedTotal, $order->total_amount);
    }

    public function test_order_soft_deletes()
    {
        $customer = CustomerModel::factory()->create();
        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending'
        ]);

        $order->delete();

        $this->assertSoftDeleted($order);
    }

    public function test_order_with_items_cascade_deletion()
    {
        $customer = CustomerModel::factory()->create();
        $product = ProductModel::factory()->create();

        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending'
        ]);

        $item = OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_value' => 8.50
        ]);


        $order->delete();

        $this->assertSoftDeleted($order);

        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'order_id' => $order->id,
            'deleted_at' => null
        ]);

        $this->assertNotNull(OrderItemModel::find($item->id));
    }

    public function test_order_items_are_deleted_when_order_is_force_deleted()
    {
        $customer = CustomerModel::factory()->create();
        $product = ProductModel::factory()->create();

        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending'
        ]);

        $item = OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_value' => 8.50
        ]);

        $order->forceDelete();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);

        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
    }

    public function test_order_has_many_items_relationship()
    {
        $order = OrderModel::make();
        $relation = $order->items();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals('order_id', $relation->getForeignKeyName());
    }

    public function test_order_item_belongs_to_order_relationship()
    {
        $orderItem = new OrderItemModel();
        $relation = $orderItem->order();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('order_id', $relation->getForeignKeyName());
    }

    public function test_order_item_belongs_to_product_relationship()
    {
        $orderItem = new OrderItemModel();
        $relation = $orderItem->product();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
    }

    public function test_order_item_can_retrieve_related_order()
    {
        $customer = CustomerModel::factory()->create();
        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'total_amount' => 0
        ]);
        $product = ProductModel::factory()->create();

        $orderItem = OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_value' => 8.50,
            'total_value' => 17.00
        ]);

        $retrievedOrder = $orderItem->order;

        $this->assertInstanceOf(OrderModel::class, $retrievedOrder);
        $this->assertEquals($order->id, $retrievedOrder->id);
    }

    public function test_order_item_can_retrieve_related_product()
    {
        $customer = CustomerModel::factory()->create();
        $order = OrderModel::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'total_amount' => 0
        ]);
        $product = ProductModel::factory()->create();

        $orderItem = OrderItemModel::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_value' => 8.50,
            'total_value' => 17.00
        ]);

        $retrievedProduct = $orderItem->product;

        $this->assertInstanceOf(ProductModel::class, $retrievedProduct);
        $this->assertEquals($product->id, $retrievedProduct->id);
    }
}
