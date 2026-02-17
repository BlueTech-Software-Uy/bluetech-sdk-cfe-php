<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;

class EmissionPointsApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function all(array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/puntoemision/todos', $query);
    }

    public function getById(int $id): array
    {
        return $this->client->request('GET', '/api/v1/puntoemision/' . $id);
    }

    public function byBranch(int $branchId, array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/puntoemision/sucursal/' . $branchId, $query);
    }
}
