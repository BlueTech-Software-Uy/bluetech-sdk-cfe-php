<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Models\WebhookCreateRequest;
use Bluetech\Sdk\Models\WebhookResponse;

class WebhooksApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function create($payload, ?int $idEmpresa = null): WebhookResponse
    {
        $body = $this->normalizePayload($payload);
        $query = $idEmpresa ? ['idEmpresa' => $idEmpresa] : [];
        $data = $this->client->request('POST', '/api/v1/webhooks', $query, $body);
        return WebhookResponse::fromArray($data);
    }

    private function normalizePayload($payload): array
    {
        if ($payload instanceof WebhookCreateRequest) {
            return $payload->toArray();
        }
        return is_array($payload) ? $payload : [];
    }
}
