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
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="client_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="unit_value", type="number", format="float", example=1500.00),
 *     @OA\Property(property="total_value", type="number", format="float", example=3000.00),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="notes", type="string", example="Entregar de manhã"),
 *     @OA\Property(property="client", ref="#/components/schemas/Client"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
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
     *     summary="Lista todos os pedidos",
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
     *     summary="Cria um novo pedido",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","client_id","quantity","unit_value","total_value","status"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2),
     *             @OA\Property(property="unit_value", type="number", format="float", example=1500.00),
     *             @OA\Property(property="total_value", type="number", format="float", example=3000.00),
     *             @OA\Property(property="status", type="string", example="pending", enum={"pending", "approved", "canceled", "delivered"}),
     *             @OA\Property(property="notes", type="string", example="Entregar de manhã")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido criado com sucesso",
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
     *     summary="Obtém um pedido específico",
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
     *     summary="Atualiza um pedido",
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
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2),
     *             @OA\Property(property="unit_value", type="number", format="float", example=1500.00),
     *             @OA\Property(property="total_value", type="number", format="float", example=3000.00),
     *             @OA\Property(property="status", type="string", example="approved", enum={"pending", "approved", "canceled", "delivered"}),
     *             @OA\Property(property="notes", type="string", example="Entregar de manhã")
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
     *     summary="Exclui um pedido",
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
