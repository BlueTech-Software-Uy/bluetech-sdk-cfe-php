<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;

class ReferenceDataApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function companies(): array
    {
        return $this->client->request('GET', '/api/v1/usuario/empresas');
    }

    public function currencies(): array
    {
        return $this->client->request('GET', '/api/v1/monedas/todas');
    }
}
