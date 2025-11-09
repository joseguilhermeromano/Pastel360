<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCreatedMail;

class OrderService
{
    protected $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createOrder(array $data)
    {
        $order = $this->repository->create($data);

        Mail::to($order->customer->mail)->queue(new OrderCreatedMail($order));

        return $order;
    }
}
