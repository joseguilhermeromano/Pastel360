<?php

namespace App\Http\Controllers;

use App\Models\OrderModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController
{
    public function index(): JsonResponse
    {
        $orders = OrderModel::with(['client', 'product'])->get();
        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'client_id' => 'required|exists:clients,id',
            'quantity' => 'required|integer|min:1',
            'unit_value' => 'required|numeric|min:0',
            'total_value' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,approved,canceled,delivered',
            'notes' => 'nullable|string'
        ]);

        $order = OrderModel::create($validated);
        return response()->json($order->load(['client', 'product']), 201);
    }

    public function show(OrderModel $order): JsonResponse
    {
        return response()->json($order->load(['client', 'product']));
    }

    public function update(Request $request, OrderModel $order): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'client_id' => 'sometimes|exists:clients,id',
            'quantity' => 'sometimes|integer|min:1',
            'unit_value' => 'sometimes|numeric|min:0',
            'total_value' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:pending,approved,canceled,delivered',
            'notes' => 'nullable|string'
        ]);

        $order->update($validated);
        return response()->json($order->load(['client', 'product']));
    }

    public function destroy(OrderModel $order): JsonResponse
    {
        $order->delete();
        return response()->json(null, 204);
    }
}
