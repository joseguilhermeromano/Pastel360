<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController
{
    public function index(): JsonResponse
    {
        $products = ProductModel::all();
        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products,sku',
            'enable' => 'boolean'
        ]);

        $product = ProductModel::create($validated);
        return response()->json($product, 201);
    }

    public function show(ProductModel $product): JsonResponse
    {
        return response()->json($product);
    }

    public function update(Request $request, ProductModel $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'photo' => 'nullable|string',
            'stock' => 'sometimes|integer|min:0',
            'sku' => 'sometimes|string|unique:products,sku,' . $product->id,
            'enable' => 'boolean'
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(ProductModel $product): JsonResponse
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
