<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\OrderRequest;
use App\Repositories\Contracts\OrderRepositoryInterface;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="customer_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="total_amount", type="number", format="float", example=45.50),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="customer",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="José Romano"),
 *         @OA\Property(property="email", type="string", example="jromano@email.com")
 *     ),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderItem")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="OrderItem",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=3),
 *     @OA\Property(property="unit_value", type="number", format="float", example=8.50),
 *     @OA\Property(property="total_value", type="number", format="float", example=25.50),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Pastel de Carne"),
 *         @OA\Property(property="description", type="string", example="Pastel de carne moída com temperos especiais")
 *     )
 * )
 */
class OrderController extends Controller
{

    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Lista todos os pedidos de pastéis",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $orders = $this->orderRepository->all();
        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Cria um novo pedido de pastéis",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer_id","status","items"},
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending", enum={"pending", "approved", "in_preparation", "ready", "delivered", "canceled"}),
     *             @OA\Property(property="notes", type="string", example="Sem cebola no pastel de carne"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id","quantity","unit_value"},
     *                     @OA\Property(property="product_id", type="integer", example=1, description="ID do pastel"),
     *                     @OA\Property(property="quantity", type="integer", example=2, description="Quantidade de pastéis"),
     *                     @OA\Property(property="unit_value", type="number", format="float", example=8.50, description="Preço unitário do pastel")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido de pastéis criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $order = $this->orderRepository->create($request->validated());
        return response()->json($order, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Obtém um pedido específico de pastéis",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(response=404, description="Pedido não encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);
        return response()->json($order);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Atualiza um pedido de pastéis",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="in_preparation", enum={"pending", "approved", "in_preparation", "ready", "delivered", "canceled"}),
     *             @OA\Property(property="notes", type="string", example="Adicionar mais um pastel de queijo"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="ID do item (para atualização)"),
     *                     @OA\Property(property="product_id", type="integer", example=2, description="ID do pastel"),
     *                     @OA\Property(property="quantity", type="integer", example=3, description="Quantidade de pastéis"),
     *                     @OA\Property(property="unit_value", type="number", format="float", example=7.50, description="Preço unitário do pastel")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(response=404, description="Pedido não encontrado"),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function update(OrderRequest $request, int $id): JsonResponse
    {
        $order = $this->orderRepository->update($id, $request->validated());
        return response()->json($order);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Exclui um pedido de pastéis",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Pedido excluído com sucesso"
     *     ),
     *     @OA\Response(response=404, description="Pedido não encontrado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->orderRepository->delete($id);
        return response()->json(null, 204);
    }
}
