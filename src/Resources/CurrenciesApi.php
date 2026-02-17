<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;

class CurrenciesApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function all(): array
    {
        return $this->client->request('GET', '/api/v1/monedas/todas');
    }
}
