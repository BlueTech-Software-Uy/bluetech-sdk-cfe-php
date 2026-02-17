<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;

class ProductsApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function variants(array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/producto/variantes', $query);
    }

    public function search(array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/producto/buscar', $query);
    }

    public function searchSimple(array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/producto/buscarSimple', $query);
    }

    public function getVariantById(int $id): array
    {
        return $this->client->request('GET', '/api/v1/producto/variante/' . $id);
    }
}
