<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductRequest;
use App\Models\ProductModel;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Storage;

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
    ) {
        $this->productRepository = $productRepository;
    }

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
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadPhoto($request, $data);
        }

        $product = $this->productRepository->create($data);
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

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

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
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadPhoto($request, $data);

            $existingProduct = $this->productRepository->find($id);
            if ($existingProduct->photo) {
                $this->deletePhoto($existingProduct->photo);
            }
        }

        $product = $this->productRepository->update($id, $data);
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
        $product = $this->productRepository->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        if ($product->photo) {
            $this->deletePhoto($product->photo);
        }

        $this->productRepository->delete($id);
        return response()->json(null, 204);
    }

    private function uploadPhoto(ProductRequest $request, $data): string
    {
        $filename = '';

        if ($request->hasFile('photo')) {
            $filename = 'TEMP-SKU.' . $request->file('photo')->getClientOriginalExtension();
            $data['photo'] = $request->file('photo')->storeAs('products', $filename, 'public');
        }

        return $filename;
    }

    private function deletePhoto(string $fileName): void
    {
        if (Storage::disk('products')->exists($fileName)) {
            Storage::disk('products')->delete($fileName);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/products/image/{filename}",
     *     summary="Obter imagem do produto",
     *     description="Retorna a imagem do produto pelo nome do arquivo",
     *     operationId="getProductImage",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="filename",
     *         in="path",
     *         required=true,
     *         description="Nome do arquivo da imagem",
     *         @OA\Schema(
     *             type="string",
     *             example="PROD-PASTELCARNE-ABC123.jpg"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagem retornada com sucesso",
     *         @OA\MediaType(
     *             mediaType="image/jpeg",
     *             @OA\Schema(type="string", format="binary")
     *         ),
     *         @OA\MediaType(
     *             mediaType="image/png",
     *             @OA\Schema(type="string", format="binary")
     *         ),
     *         @OA\MediaType(
     *             mediaType="image/gif",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Imagem não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Imagem não encontrada")
     *         )
     *     )
     * )
     */
    public function getImage(string $filename)
    {
        $path = storage_path('app/public/products/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
