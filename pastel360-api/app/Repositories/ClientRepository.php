<?php

namespace App\Repositories;

use App\Models\ClientModel;
use App\Repositories\Contracts\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    protected ClientModel $client;

    public function __construct(ClientModel $client)
    {
        $this->client = $client;
    }

    public function all()
    {
        return $this->client->all();
    }

    public function find(int $id)
    {
        return $this->client->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->client->create($data);
    }

    public function update(int $id, array $data)
    {
        $client = $this->find($id);
        $client->update($data);
        return $client;
    }

    public function delete(int $id)
    {
        $client = $this->find($id);
        return $client->delete();
    }
}
