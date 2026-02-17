<?php

namespace Bluetech\Sdk\Tests;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Resources\ReferenceDataApi;
use Bluetech\Sdk\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ReferenceDataApiTest extends TestCase
{
    public function testCompaniesUsesExpectedEndpoint(): void
    {
        $response = new Response(200, [], json_encode([]));
        $httpClient = new FakeHttpClient([$response]);
        $factory = new HttpFactory();
        $client = new ApiClient(new Config('https://example.test'), $httpClient, $factory, $factory);
        $api = new ReferenceDataApi($client);

        $api->companies();

        $this->assertCount(1, $httpClient->requests);
        $request = $httpClient->requests[0];
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/api/v1/usuario/empresas', $request->getUri()->getPath());
    }

    public function testCurrenciesUsesExpectedEndpoint(): void
    {
        $response = new Response(200, [], json_encode([]));
        $httpClient = new FakeHttpClient([$response]);
        $factory = new HttpFactory();
        $client = new ApiClient(new Config('https://example.test'), $httpClient, $factory, $factory);
        $api = new ReferenceDataApi($client);

        $api->currencies();

        $this->assertCount(1, $httpClient->requests);
        $request = $httpClient->requests[0];
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/api/v1/monedas/todas', $request->getUri()->getPath());
    }
}
