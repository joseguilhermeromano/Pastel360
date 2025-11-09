<?php

namespace App\Mail;

use App\Models\OrderModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(OrderModel $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject("Detalhes do seu pedido #{$this->order->id}")
            ->markdown('emails.orders.created');
    }
}
