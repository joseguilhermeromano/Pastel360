<?php

namespace App\Http\Controllers;

use App\Models\ClientModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController
{
    public function index(): JsonResponse
    {
        $clients = ClientModel::all();
        return response()->json($clients);
    }

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

    public function show(ClientModel $client): JsonResponse
    {
        return response()->json($client);
    }

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

    public function destroy(ClientModel $client): JsonResponse
    {
        $client->delete();
        return response()->json(null, 204);
    }
}
