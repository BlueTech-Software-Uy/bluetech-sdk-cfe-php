<?php

namespace Bluetech\Sdk\Auth;

use Bluetech\Sdk\Models\AuthTokens;

class FileTokenStore implements TokenStoreInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function save(AuthTokens $tokens): void
    {
        $data = [
            'token' => $tokens->token ?? null,
            'refresh_token' => $tokens->refresh_token ?? null,
            'expires_at' => $tokens->expires_at ?? null,
        ];
        $json = json_encode($data);
        if ($json === false) {
            return;
        }
        @file_put_contents($this->path, $json);
    }

    public function load(): ?AuthTokens
    {
        if (!file_exists($this->path)) {
            return null;
        }
        $raw = @file_get_contents($this->path);
        if ($raw === false || $raw === '') {
            return null;
        }
        $data = json_decode($raw, true);
        if (!is_array($data) || empty($data['token'])) {
            return null;
        }
        return AuthTokens::fromArray($data);
    }

    public function clear(): void
    {
        if (file_exists($this->path)) {
            @unlink($this->path);
        }
    }
}
