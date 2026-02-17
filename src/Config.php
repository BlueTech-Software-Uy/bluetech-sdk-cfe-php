<?php

namespace Bluetech\Sdk;

use Bluetech\Sdk\Auth\TokenStoreInterface;

class Config
{
    private string $baseUrl;
    private ?string $token;
    private ?string $refreshToken;
    private string $userAgent;
    private int $timeout;
    private int $retryMax;
    private int $retryBackoffMs;
    private ?TokenStoreInterface $tokenStore;

    public function __construct(string $baseUrl, ?string $token = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
        $this->refreshToken = null;
        $this->userAgent = 'bluetech-sdk-php/0.1.0';
        $this->timeout = 30;
        $this->retryMax = 2;
        $this->retryBackoffMs = 300;
        $this->tokenStore = null;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function setTokenStore(?TokenStoreInterface $store): void
    {
        $this->tokenStore = $store;
        if ($store && $this->token === null) {
            $tokens = $store->load();
            if ($tokens) {
                $this->token = $tokens->token ?? $this->token;
                if (!empty($tokens->refresh_token)) {
                    $this->refreshToken = $tokens->refresh_token;
                }
            }
        }
    }

    public function getTokenStore(): ?TokenStoreInterface
    {
        return $this->tokenStore;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $seconds): void
    {
        $this->timeout = $seconds;
    }

    public function getRetryMax(): int
    {
        return $this->retryMax;
    }

    public function setRetryMax(int $retryMax): void
    {
        $this->retryMax = $retryMax;
    }

    public function getRetryBackoffMs(): int
    {
        return $this->retryBackoffMs;
    }

    public function setRetryBackoffMs(int $retryBackoffMs): void
    {
        $this->retryBackoffMs = $retryBackoffMs;
    }
}
