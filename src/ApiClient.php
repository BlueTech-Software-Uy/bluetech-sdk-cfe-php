<?php

namespace Bluetech\Sdk;

use Bluetech\Sdk\Exceptions\ApiException;
use Bluetech\Sdk\Exceptions\ForbiddenException;
use Bluetech\Sdk\Exceptions\IdempotencyConflictException;
use Bluetech\Sdk\Exceptions\NotFoundException;
use Bluetech\Sdk\Exceptions\RateLimitException;
use Bluetech\Sdk\Exceptions\ServerException;
use Bluetech\Sdk\Exceptions\UnauthorizedException;
use Bluetech\Sdk\Exceptions\ValidationException;
use Bluetech\Sdk\Http\RetryPolicy;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    private Config $config;
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private RetryPolicy $retryPolicy;

    public function __construct(
        Config $config,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->retryPolicy = new RetryPolicy($config->getRetryMax(), $config->getRetryBackoffMs());
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?string $idempotencyKey = null
    ): array {
        $attempt = 0;
        $refreshed = false;
        do {
            $request = $this->buildRequest($method, $path, $query, $body, $headers, $idempotencyKey);
            $response = $this->httpClient->sendRequest($request);
            $status = $response->getStatusCode();

            if ($status === 401 && !$refreshed && $this->config->getRefreshToken()) {
                $refreshed = $this->refreshAccessToken();
                if ($refreshed) {
                    continue;
                }
            }

            if (!$this->retryPolicy->shouldRetry($status) || $attempt >= $this->retryPolicy->getMaxRetries()) {
                return $this->handleResponse($response);
            }
            $attempt++;
            usleep($this->retryPolicy->getBackoffMs() * 1000);
        } while (true);
    }

    private function buildRequest(
        string $method,
        string $path,
        array $query,
        ?array $body,
        array $headers,
        ?string $idempotencyKey
    ) {
        $url = $this->buildUrl($path, $query);
        $request = $this->requestFactory->createRequest($method, $url);
        $request = $request->withHeader('Accept', 'application/json')
            ->withHeader('User-Agent', $this->config->getUserAgent());

        if ($this->config->getToken()) {
            $request = $request->withHeader('Authorization', 'Bearer ' . $this->config->getToken());
        }
        if ($idempotencyKey) {
            $request = $request->withHeader('Idempotency-Key', $idempotencyKey);
        }
        foreach ($headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        if ($body !== null) {
            $payload = json_encode($body);
            $stream = $this->streamFactory->createStream($payload === false ? '' : $payload);
            $request = $request->withHeader('Content-Type', 'application/json')->withBody($stream);
        }

        return $request;
    }

    private function refreshAccessToken(): bool
    {
        $refreshToken = $this->config->getRefreshToken();
        if (!$refreshToken) {
            return false;
        }
        $request = $this->buildRequest('POST', '/auth/refresh', [], ['refresh_token' => $refreshToken], [], null);
        $response = $this->httpClient->sendRequest($request);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            return false;
        }
        $body = (string)$response->getBody();
        $data = $body !== '' ? json_decode($body, true) : [];
        if (!is_array($data) || empty($data['token'])) {
            return false;
        }
        $this->config->setToken($data['token']);
        if (!empty($data['refresh_token'])) {
            $this->config->setRefreshToken($data['refresh_token']);
        }
        $store = $this->config->getTokenStore();
        if ($store) {
            $store->save(\Bluetech\Sdk\Models\AuthTokens::fromArray($data));
        }
        return true;
    }

    private function buildUrl(string $path, array $query): string
    {
        $url = rtrim($this->config->getBaseUrl(), '/') . '/' . ltrim($path, '/');
        if (!empty($query)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($query);
        }
        return $url;
    }

    private function handleResponse(ResponseInterface $response): array
    {
        $status = $response->getStatusCode();
        $body = (string)$response->getBody();
        $data = $body !== '' ? json_decode($body, true) : [];
        $data = is_array($data) ? $data : [];

        if ($status >= 200 && $status < 300) {
            return $data;
        }

        $message = $data['error'] ?? $response->getReasonPhrase();
        $errorCode = $data['code'] ?? null;
        $details = $data['details'] ?? [];
        $requestId = $response->getHeaderLine('x-request-id') ?: ($data['request_id'] ?? null);

        if ($status === 401) {
            throw new UnauthorizedException($status, $message, $errorCode, $details, $requestId);
        }
        if ($status === 403) {
            throw new ForbiddenException($status, $message, $errorCode, $details, $requestId);
        }
        if ($status === 404) {
            throw new NotFoundException($status, $message, $errorCode, $details, $requestId);
        }
        if ($status === 409) {
            throw new IdempotencyConflictException($status, $message, $errorCode, $details, $requestId);
        }
        if ($status === 422 || $status === 400) {
            throw new ValidationException($status, $message, $errorCode, $details, $requestId);
        }
        if ($status === 429) {
            throw new RateLimitException($status, $message, $errorCode, $details, $requestId);
        }
        if ($status >= 500) {
            throw new ServerException($status, $message, $errorCode, $details, $requestId);
        }

        throw new ApiException($status, $message, $errorCode, $details, $requestId);
    }
}
