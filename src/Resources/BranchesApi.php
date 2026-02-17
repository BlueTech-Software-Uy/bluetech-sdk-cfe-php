<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;

class BranchesApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function all(array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/sucursal/todas', $query);
    }

    public function getById(int $id): array
    {
        return $this->client->request('GET', '/api/v1/sucursal/' . $id);
    }
}
