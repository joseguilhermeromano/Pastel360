<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\ClientRequest;
use App\Repositories\Contracts\ClientRepositoryInterface;

/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="José Romano"),
 *     @OA\Property(property="mail", type="string", format="email", example="jromano@teste.com"),
 *     @OA\Property(property="phone", type="string", example="11999999999"),
 *     @OA\Property(property="birthdate", type="string", format="date", example="1996-01-01"),
 *     @OA\Property(property="place", type="string", example="Rua Principal"),
 *     @OA\Property(property="number", type="string", example="123"),
 *     @OA\Property(property="zipcode", type="string", example="12345678"),
 *     @OA\Property(property="district", type="string", example="Centro"),
 *     @OA\Property(property="complement", type="string", example="Apto 45"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ClientController extends Controller
{

    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     summary="Lista todos os clientes",
     *     tags={"Clients"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de clientes",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Client"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $clients = $this->clientRepository->all();
        return response()->json($clients);
    }

    /**
     * @OA\Post(
     *     path="/api/clients",
     *     summary="Cria um novo cliente",
     *     tags={"Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","mail","phone","birthdate","place","number","zipcode","district"},
     *             @OA\Property(property="name", type="string", example="José Romano"),
     *             @OA\Property(property="mail", type="string", format="email", example="jromano@teste.com"),
     *             @OA\Property(property="phone", type="string", example="11999999999"),
     *             @OA\Property(property="birthdate", type="string", format="date", example="1996-01-01"),
     *             @OA\Property(property="place", type="string", example="Rua Principal"),
     *             @OA\Property(property="number", type="string", example="123"),
     *             @OA\Property(property="zipcode", type="string", example="12345678"),
     *             @OA\Property(property="district", type="string", example="Centro"),
     *             @OA\Property(property="complement", type="string", example="Apto 45")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     ),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function store(ClientRequest $request): JsonResponse
    {
        $client = $this->clientRepository->create($request->validated());
        return response()->json($client, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/clients/{id}",
     *     summary="Obtém um cliente específico",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     ),
     *     @OA\Response(response=404, description="Cliente não encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $client = $this->clientRepository->find($id);
        return response()->json($client);
    }

    /**
     * @OA\Put(
     *     path="/api/clients/{id}",
     *     summary="Atualiza um cliente",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="José Romano"),
     *             @OA\Property(property="mail", type="string", format="email", example="jromano@teste.com"),
     *             @OA\Property(property="phone", type="string", example="11999999999"),
     *             @OA\Property(property="birthdate", type="string", format="date", example="1996-01-01"),
     *             @OA\Property(property="place", type="string", example="Rua Principal"),
     *             @OA\Property(property="number", type="string", example="123"),
     *             @OA\Property(property="zipcode", type="string", example="12345678"),
     *             @OA\Property(property="district", type="string", example="Centro"),
     *             @OA\Property(property="complement", type="string", example="Apto 45")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     ),
     *     @OA\Response(response=404, description="Cliente não encontrado"),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function update(ClientRequest $request, int $id): JsonResponse
    {
        $client = $this->clientRepository->update($id, $request->validated());
        return response()->json($client);
    }

    /**
     * @OA\Delete(
     *     path="/api/clients/{id}",
     *     summary="Exclui um cliente",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Cliente excluído com sucesso"
     *     ),
     *     @OA\Response(response=404, description="Cliente não encontrado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->clientRepository->delete($id);
        return response()->json(null, 204);
    }
}
