<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Pastel de Carne"),
 *     @OA\Property(property="description", type="string", example="Pastel de carne moída com temperos especiais"),
 *     @OA\Property(property="price", type="number", format="float", example=8.50),
 *     @OA\Property(property="photo", type="string", example="pastel-carne.jpg"),
 *     @OA\Property(property="stock", type="integer", example=50),
 *     @OA\Property(property="sku", type="string", example="PASTEL-CARNE-001"),
 *     @OA\Property(property="enable", type="boolean", example=true),
 *     @OA\Property(property="category", type="string", example="salgado"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ProductController extends Controller
{

    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Lista todos os pastéis",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pastéis",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->all();
        return response()->json($products);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Cria um novo pastel",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","stock","sku"},
     *             @OA\Property(property="name", type="string", example="Pastel de Queijo", description="Nome do pastel"),
     *             @OA\Property(property="description", type="string", example="Pastel de queijo mussarela derretido", description="Descrição do pastel"),
     *             @OA\Property(property="price", type="number", format="float", example=7.50, description="Preço do pastel"),
     *             @OA\Property(property="photo", type="string", example="pastel-queijo.jpg", description="Foto do pastel"),
     *             @OA\Property(property="stock", type="integer", example=30, description="Quantidade em estoque"),
     *             @OA\Property(property="sku", type="string", example="PASTEL-QUEIJO-002", description="Código SKU do pastel"),
     *             @OA\Property(property="enable", type="boolean", example=true, description="Disponível para venda"),
     *             @OA\Property(property="category", type="string", example="salgado", description="Categoria: salgado, doce, especial")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pastel criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productRepository->create($request->validated());
        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Obtém um pastel específico",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pastel encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=404, description="Pastel não encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        return response()->json($product);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Atualiza um pastel",
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
     *             @OA\Property(property="name", type="string", example="Pastel de Frango", description="Nome do pastel"),
     *             @OA\Property(property="description", type="string", example="Pastel de frango desfiado com catupiry", description="Descrição do pastel"),
     *             @OA\Property(property="price", type="number", format="float", example=8.00, description="Preço do pastel"),
     *             @OA\Property(property="photo", type="string", example="pastel-frango.jpg", description="Foto do pastel"),
     *             @OA\Property(property="stock", type="integer", example=25, description="Quantidade em estoque"),
     *             @OA\Property(property="sku", type="string", example="PASTEL-FRANGO-003", description="Código SKU do pastel"),
     *             @OA\Property(property="enable", type="boolean", example=true, description="Disponível para venda"),
     *             @OA\Property(property="category", type="string", example="salgado", description="Categoria: salgado, doce, especial")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pastel atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=404, description="Pastel não encontrado"),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productRepository->update($id, $request->validated());
        return response()->json($product);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Exclui um pastel",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Pastel excluído com sucesso"
     *     ),
     *     @OA\Response(response=404, description="Pastel não encontrado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->productRepository->delete($id);
        return response()->json(null, 204);
    }
}
