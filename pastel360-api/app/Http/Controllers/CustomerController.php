<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\CustomerRequest;
use App\Repositories\Contracts\CustomerRepositoryInterface;

/**
 * @OA\Schema(
 *     schema="Customer",
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
class CustomerController extends Controller
{

    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Lista todos os customeres",
     *     tags={"Customers"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de customeres",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Customer"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $customers = $this->customerRepository->all();
        return response()->json($customers);
    }

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Cria um novo customere",
     *     tags={"Customers"},
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
     *         description="Customere criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function store(CustomerRequest $request): JsonResponse
    {
        $customer = $this->customerRepository->create($request->validated());
        return response()->json($customer, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     summary="Obtém um customere específico",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customere encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(response=404, description="Customere não encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);
        return response()->json($customer);
    }

    /**
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     summary="Atualiza um customere",
     *     tags={"Customers"},
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
     *         description="Customere atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(response=404, description="Customere não encontrado"),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function update(CustomerRequest $request, int $id): JsonResponse
    {
        $customer = $this->customerRepository->update($id, $request->validated());
        return response()->json($customer);
    }

    /**
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     summary="Exclui um customere",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Customere excluído com sucesso"
     *     ),
     *     @OA\Response(response=404, description="Customere não encontrado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->customerRepository->delete($id);
        return response()->json(null, 204);
    }
}
