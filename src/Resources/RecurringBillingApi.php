<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;

class RecurringBillingApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function preview(int $id): array
    {
        return $this->client->request('GET', '/api/v1/facturacionrecurrente/preview/' . $id);
    }

    public function execute(int $id, ?string $idempotencyKey = null): array
    {
        return $this->client->request('POST', '/api/v1/facturacionrecurrente/ejecutar/' . $id, [], null, [], $idempotencyKey);
    }

    public function history(int $id): array
    {
        return $this->client->request('POST', '/api/v1/facturacionrecurrente/logs/' . $id);
    }

    public function catalogs(): array
    {
        return $this->client->request('GET', '/api/v1/facturacionrecurrente/catalogos');
    }
}
