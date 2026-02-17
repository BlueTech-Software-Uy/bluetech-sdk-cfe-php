<?php

namespace Bluetech\Sdk\Tests;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Resources\RecurringBillingApi;
use Bluetech\Sdk\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RecurringBillingApiTest extends TestCase
{
    public function testCatalogsUsesExpectedEndpoint(): void
    {
        $response = new Response(200, [], json_encode(['frecuencias' => []]));
        $httpClient = new FakeHttpClient([$response]);
        $factory = new HttpFactory();
        $client = new ApiClient(new Config('https://example.test'), $httpClient, $factory, $factory);
        $api = new RecurringBillingApi($client);

        $api->catalogs();

        $this->assertCount(1, $httpClient->requests);
        $request = $httpClient->requests[0];
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/api/v1/facturacionrecurrente/catalogos', $request->getUri()->getPath());
    }
}
