<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Models\UsageAggregate;
use Bluetech\Sdk\Models\UsageAggregatesResponse;
use Bluetech\Sdk\Models\UsageEvent;
use Bluetech\Sdk\Models\UsageEventsResponse;
use Bluetech\Sdk\Models\Pagination;

class UsageApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function createUsageEvents($events, ?string $idempotencyKey = null): UsageEventsResponse
    {
        $body = $this->normalizeEvents($events);
        $data = $this->client->request('POST', '/api/v1/usage-events', [], $body, [], $idempotencyKey);
        $response = UsageEventsResponse::fromArray($data);
        $response->events = array_map(function ($event) {
            return UsageEvent::fromArray($event);
        }, $data['events'] ?? []);
        return $response;
    }

    public function getContractUsage(int $contractId, string $start, string $end, array $options = []): UsageAggregatesResponse
    {
        $query = array_merge(['start' => $start, 'end' => $end], $options);
        $data = $this->client->request('GET', '/api/v1/contracts/' . $contractId . '/usage', $query);
        $response = UsageAggregatesResponse::fromArray($data);
        $response->data = array_map(function ($row) {
            return UsageAggregate::fromArray($row);
        }, $data['data'] ?? []);
        if (isset($data['pagination']) && is_array($data['pagination'])) {
            $response->pagination = Pagination::fromArray($data['pagination']);
        }
        return $response;
    }

    private function normalizeEvents($events): array
    {
        if ($events instanceof UsageEvent) {
            return [$events->toArray()];
        }
        if (is_array($events)) {
            $isList = array_keys($events) === range(0, count($events) - 1);
            if ($isList) {
                return array_map(function ($event) {
                    return $event instanceof UsageEvent ? $event->toArray() : $event;
                }, $events);
            }
            return $events;
        }
        return [];
    }
}
