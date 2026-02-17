<?php

namespace Bluetech\Sdk\Tests;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Exceptions\IdempotencyConflictException;
use Bluetech\Sdk\Exceptions\UnauthorizedException;
use Bluetech\Sdk\Exceptions\ValidationException;
use Bluetech\Sdk\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    public function testUnauthorizedThrowsException(): void
    {
        $response = new Response(401, [], json_encode(['error' => 'Unauthorized']));
        $client = $this->makeClient([$response]);

        $this->expectException(UnauthorizedException::class);
        $client->request('GET', '/api/v1/contracts/1/state');
    }

    public function testValidationThrowsException(): void
    {
        $response = new Response(422, [], json_encode(['error' => 'Invalid']));
        $client = $this->makeClient([$response]);

        $this->expectException(ValidationException::class);
        $client->request('POST', '/api/v1/usage-events', [], ['contractId' => 1]);
    }

    public function testIdempotencyConflictThrowsException(): void
    {
        $response = new Response(409, [], json_encode(['error' => 'Duplicate']));
        $client = $this->makeClient([$response]);

        $this->expectException(IdempotencyConflictException::class);
        $client->request('POST', '/api/v1/usage-events', [], ['contractId' => 1], [], 'dup-key');
    }

    private function makeClient(array $responses): ApiClient
    {
        $config = new Config('https://example.test');
        $httpClient = new FakeHttpClient($responses);
        $factory = new HttpFactory();
        return new ApiClient($config, $httpClient, $factory, $factory);
    }
}
