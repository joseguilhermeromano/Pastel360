<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Notebook Dell"),
 *     @OA\Property(property="price", type="number", format="float", example=2999.99),
 *     @OA\Property(property="photo", type="string", example="notebook.jpg"),
 *     @OA\Property(property="stock", type="integer", example=50),
 *     @OA\Property(property="sku", type="string", example="DELL-NB-001"),
 *     @OA\Property(property="enable", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ProductController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Lista todos os produtos",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $products = ProductModel::all();
        return response()->json($products);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Cria um novo produto",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","stock","sku"},
     *             @OA\Property(property="name", type="string", example="Notebook Dell"),
     *             @OA\Property(property="price", type="number", format="float", example=2999.99),
     *             @OA\Property(property="photo", type="string", example="notebook.jpg"),
     *             @OA\Property(property="stock", type="integer", example=50),
     *             @OA\Property(property="sku", type="string", example="DELL-NB-001"),
     *             @OA\Property(property="enable", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Obtém um produto específico",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     */
    public function show(ProductModel $product): JsonResponse
    {
        return response()->json($product);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Atualiza um produto",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Notebook Dell"),
     *             @OA\Property(property="price", type="number", format="float", example=2999.99),
     *             @OA\Property(property="photo", type="string", example="notebook.jpg"),
     *             @OA\Property(property="stock", type="integer", example=50),
     *             @OA\Property(property="sku", type="string", example="DELL-NB-001"),
     *             @OA\Property(property="enable", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Exclui um produto",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Produto excluído com sucesso"
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     */
    public function destroy(ProductModel $product): JsonResponse
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
