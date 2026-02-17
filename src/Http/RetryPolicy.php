<?php

namespace Bluetech\Sdk\Http;

class RetryPolicy
{
    private int $maxRetries;
    private int $backoffMs;

    public function __construct(int $maxRetries, int $backoffMs)
    {
        $this->maxRetries = $maxRetries;
        $this->backoffMs = $backoffMs;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function getBackoffMs(): int
    {
        return $this->backoffMs;
    }

    public function shouldRetry(int $statusCode): bool
    {
        return in_array($statusCode, [429, 503], true);
    }
}
