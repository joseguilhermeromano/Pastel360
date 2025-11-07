<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\OrderModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;
    public function test_order_creation()
    {
        $order = OrderModel::make([
            'product_id' => 1,
            'client_id' => 1,
            'quantity' => 2,
            'unit_value' => 10.50,
            'total_value' => 21.00,
            'status' => 'pending'
        ]);

        $this->assertInstanceOf(OrderModel::class, $order);
        $this->assertEquals(21.00, $order->total_value);
    }

    public function test_order_belongs_to_client()
    {
        $order = OrderModel::make(['client_id' => 1]);
        $relation = $order->client();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('client_id', $relation->getForeignKeyName());
    }

    public function test_order_belongs_to_product()
    {
        $order = OrderModel::make(['product_id' => 1]);
        $relation = $order->product();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
    }
}
