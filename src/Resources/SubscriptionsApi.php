<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Models\ContractPayload;
use Bluetech\Sdk\Models\ContractState;
use Bluetech\Sdk\Models\Pagination;
use Bluetech\Sdk\Models\UsageStatusHistoryEntry;
use Bluetech\Sdk\Models\UsageStatusHistoryResponse;

class SubscriptionsApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function listContracts(array $query = []): array
    {
        return $this->client->request('GET', '/api/v1/facturacionrecurrente/buscar', $query);
    }

    public function getContractById(int $id): array
    {
        return $this->client->request('POST', '/api/v1/facturacionrecurrente/' . $id);
    }

    public function createContract($payload): array
    {
        $body = $this->normalizePayload($payload);
        return $this->client->request('POST', '/api/v1/facturacionrecurrente/crear', [], $body);
    }

    public function updateContract($payload): array
    {
        $body = $this->normalizePayload($payload);
        return $this->client->request('POST', '/api/v1/facturacionrecurrente/editar', [], $body);
    }

    public function activateContract(int $id): array
    {
        return $this->client->request('POST', '/api/v1/facturacionrecurrente/activar/' . $id);
    }

    public function deactivateContract(int $id): array
    {
        return $this->client->request('POST', '/api/v1/facturacionrecurrente/desactivar/' . $id);
    }

    public function deleteNonExecuted(int $id): array
    {
        return $this->client->request('POST', '/api/v1/facturacionrecurrente/eliminar/' . $id);
    }

    public function contractState(int $id): ContractState
    {
        $data = $this->client->request('GET', '/api/v1/contracts/' . $id . '/state');
        return ContractState::fromArray($data);
    }

    public function contractStatusHistory(int $id, array $query = []): UsageStatusHistoryResponse
    {
        $data = $this->client->request('GET', '/api/v1/contracts/' . $id . '/status-history', $query);
        $response = UsageStatusHistoryResponse::fromArray($data);
        $response->data = array_map(function ($row) {
            return UsageStatusHistoryEntry::fromArray($row);
        }, $data['data'] ?? []);
        if (isset($data['pagination']) && is_array($data['pagination'])) {
            $response->pagination = Pagination::fromArray($data['pagination']);
        }
        return $response;
    }

    private function normalizePayload($payload): array
    {
        if ($payload instanceof ContractPayload) {
            return $payload->toArray();
        }
        return is_array($payload) ? $payload : [];
    }
}
