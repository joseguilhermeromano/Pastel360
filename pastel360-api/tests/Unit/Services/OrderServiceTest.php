<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\CustomerModel;
use App\Services\OrderService;
use App\Mail\OrderCreatedMail;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;
    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new OrderRepository(new OrderModel());

        $this->service = new OrderService($repository);
    }

    public function test_it_creates_order_and_sends_email()
    {
        Mail::fake();

        $customer = CustomerModel::factory()->create();
        $product = ProductModel::factory()->create(['id' => 1]);

        $data = [
            'customer_id' => $customer->id,
            'total_amount' => 20,
            'notes' => 'teste',
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_value' => 10,
                    'total_value' => 20
                ]
            ]
        ];

        $order = $this->service->createOrder($data);

        $this->assertEquals(20, $order->total_amount);
        $this->assertCount(1, $order->items);

        Mail::assertQueued(OrderCreatedMail::class);
    }

    public function test_build_sets_subject_and_markdown_view()
    {
        $order = OrderModel::factory()->create();

        $mail = new OrderCreatedMail($order);
        $mail->build();

        $this->assertEquals(
            "Detalhes do seu pedido #{$order->id}",
            $mail->subject
        );

        $this->assertEquals('emails.orders.created', $mail->markdown);
    }
}
