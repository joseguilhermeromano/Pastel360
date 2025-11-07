<?php

namespace App\Http\Controllers;

use App\Models\ClientModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
        $clients = ClientModel::all();
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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mail' => 'required|email|unique:clients,mail',
            'phone' => 'required|string|max:20',
            'birthdate' => 'required|date',
            'place' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'zipcode' => 'required|string|max:9',
            'district' => 'required|string|max:255',
            'complement' => 'nullable|string|max:255'
        ]);

        $client = ClientModel::create($validated);
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
    public function show(ClientModel $client): JsonResponse
    {
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
    public function update(Request $request, ClientModel $client): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'mail' => 'sometimes|email|unique:clients,mail,' . $client->id,
            'phone' => 'sometimes|string|max:20',
            'birthdate' => 'sometimes|date',
            'place' => 'sometimes|string|max:255',
            'number' => 'sometimes|string|max:10',
            'zipcode' => 'sometimes|string|max:9',
            'district' => 'sometimes|string|max:255',
            'complement' => 'nullable|string|max:255'
        ]);

        $client->update($validated);
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
    public function destroy(ClientModel $client): JsonResponse
    {
        $client->delete();
        return response()->json(null, 204);
    }
}
