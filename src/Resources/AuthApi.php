<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Models\AuthTokens;

class AuthApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function login(string $username, string $secret): AuthTokens
    {
        $data = $this->client->request('POST', '/api/auth/login', [], [
            'username' => $username,
            'secret' => $secret,
        ]);
        $tokens = AuthTokens::fromArray($data);
        $store = $this->client->getConfig()->getTokenStore();
        if ($store) {
            $store->save($tokens);
        }
        return $tokens;
    }

    public function exchangeRefreshToken(string $refreshToken): AuthTokens
    {
        $data = $this->client->request('POST', '/auth/refresh', [], [
            'refresh_token' => $refreshToken,
        ]);
        $tokens = AuthTokens::fromArray($data);
        $store = $this->client->getConfig()->getTokenStore();
        if ($store) {
            $store->save($tokens);
        }
        return $tokens;
    }

    public function loginAndSetToken(string $username, string $secret): AuthTokens
    {
        $tokens = $this->login($username, $secret);
        $this->client->getConfig()->setToken($tokens->token);
        if (!empty($tokens->refresh_token)) {
            $this->client->getConfig()->setRefreshToken($tokens->refresh_token);
        }
        return $tokens;
    }
}
