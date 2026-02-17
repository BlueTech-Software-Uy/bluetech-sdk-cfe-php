<?php

namespace Bluetech\Sdk\Auth;

use Bluetech\Sdk\Models\AuthTokens;

interface TokenStoreInterface
{
    public function save(AuthTokens $tokens): void;

    public function load(): ?AuthTokens;

    public function clear(): void;
}
