<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Models\Pagination;
use Bluetech\Sdk\Models\PriceBook;
use Bluetech\Sdk\Models\PriceBookCreateRequest;
use Bluetech\Sdk\Models\PriceBookItem;
use Bluetech\Sdk\Models\PriceBookListResponse;
use Bluetech\Sdk\Models\PriceBookVersion;
use Bluetech\Sdk\Models\PriceBookVersionsResponse;

class PriceBooksApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function create($payload, ?int $idEmpresa = null): PriceBook
    {
        $body = $this->normalizePayload($payload);
        $query = $idEmpresa ? ['idEmpresa' => $idEmpresa] : [];
        $data = $this->client->request('POST', '/api/v1/price-books', $query, $body);
        return PriceBook::fromArray($data);
    }

    public function list(int $idEmpresa, int $page = 1, int $perPage = 50): PriceBookListResponse
    {
        $query = ['idEmpresa' => $idEmpresa, 'page' => $page, 'per_page' => $perPage];
        $data = $this->client->request('GET', '/api/v1/price-books', $query);
        $response = PriceBookListResponse::fromArray($data);
        $response->data = array_map(function ($row) {
            return PriceBook::fromArray($row);
        }, $data['data'] ?? []);
        if (isset($data['pagination']) && is_array($data['pagination'])) {
            $response->pagination = Pagination::fromArray($data['pagination']);
        }
        return $response;
    }

    public function versions(int $priceBookId, int $page = 1, int $perPage = 50): PriceBookVersionsResponse
    {
        $query = ['page' => $page, 'per_page' => $perPage];
        $data = $this->client->request('GET', '/api/v1/price-books/' . $priceBookId . '/versions', $query);
        $response = PriceBookVersionsResponse::fromArray($data);
        $response->data = array_map(function ($row) {
            $version = PriceBookVersion::fromArray($row);
            $version->items = array_map(function ($item) {
                return PriceBookItem::fromArray($item);
            }, $row['items'] ?? []);
            return $version;
        }, $data['data'] ?? []);
        if (isset($data['pagination']) && is_array($data['pagination'])) {
            $response->pagination = Pagination::fromArray($data['pagination']);
        }
        return $response;
    }

    private function normalizePayload($payload): array
    {
        if ($payload instanceof PriceBookCreateRequest) {
            $data = $payload->toArray();
            $data['items'] = array_map(function ($item) {
                return $item instanceof PriceBookItem ? $item->toArray() : $item;
            }, $data['items'] ?? []);
            return $data;
        }
        if (is_array($payload)) {
            return $payload;
        }
        return [];
    }
}
