<?php

namespace Bluetech\Sdk\Tests;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Models\UsageEvent;
use Bluetech\Sdk\Resources\UsageApi;
use Bluetech\Sdk\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class UsageApiTest extends TestCase
{
    public function testCreateUsageEventsSendsIdempotencyKey(): void
    {
        $payload = [
            'Res' => 'OK',
            'created' => 1,
            'ignored' => 0,
            'events' => [
                [
                    'contractId' => 10,
                    'eventId' => 'evt-1',
                    'occurredAt' => '2026-01-01T00:00:00Z',
                    'quantity' => 1,
                    'unit' => 'GB',
                    'metric' => 'storage',
                ]
            ]
        ];
        $response = new Response(201, [], json_encode($payload));

        $httpClient = new FakeHttpClient([$response]);
        $factory = new HttpFactory();
        $client = new ApiClient(new Config('https://example.test'), $httpClient, $factory, $factory);
        $api = new UsageApi($client);

        $event = UsageEvent::fromArray([
            'contractId' => 10,
            'eventId' => 'evt-1',
            'occurredAt' => '2026-01-01T00:00:00Z',
            'quantity' => 1,
            'unit' => 'GB',
            'metric' => 'storage',
        ]);

        $api->createUsageEvents($event, 'idem-123');

        $this->assertCount(1, $httpClient->requests);
        $request = $httpClient->requests[0];
        $this->assertSame('idem-123', $request->getHeaderLine('Idempotency-Key'));
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
    }
}
