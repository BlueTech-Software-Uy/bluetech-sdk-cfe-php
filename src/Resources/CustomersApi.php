<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;

class CustomersApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function all(array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/cliente/todos', $query);
    }

    public function search(array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/cliente/buscar', $query);
    }

    public function getById(int $id): array
    {
        return $this->client->request('GET', '/api/v1/cliente/' . $id);
    }
}
