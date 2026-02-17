<?php

namespace Bluetech\Sdk\Tests;

use Bluetech\Sdk\Auth\FileTokenStore;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Models\AuthTokens;
use PHPUnit\Framework\TestCase;

class FileTokenStoreTest extends TestCase
{
    public function testSaveAndLoadTokens(): void
    {
        $path = $this->makeTempPath();
        $store = new FileTokenStore($path);

        $tokens = new AuthTokens();
        $tokens->token = 'access';
        $tokens->refresh_token = 'refresh';
        $tokens->expires_at = '2026-02-10T12:00:00Z';
        $store->save($tokens);

        $loaded = $store->load();
        $this->assertNotNull($loaded);
        $this->assertSame('access', $loaded->token);
        $this->assertSame('refresh', $loaded->refresh_token);

        @unlink($path);
    }

    public function testConfigLoadsFromTokenStoreWhenEmpty(): void
    {
        $path = $this->makeTempPath();
        $store = new FileTokenStore($path);

        $tokens = new AuthTokens();
        $tokens->token = 'cached';
        $tokens->refresh_token = 'cached-refresh';
        $store->save($tokens);

        $config = new Config('https://example.test');
        $config->setTokenStore($store);

        $this->assertSame('cached', $config->getToken());
        $this->assertSame('cached-refresh', $config->getRefreshToken());

        @unlink($path);
    }

    private function makeTempPath(): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            . 'bluetech-sdk-token-' . uniqid('', true) . '.json';
    }
}
