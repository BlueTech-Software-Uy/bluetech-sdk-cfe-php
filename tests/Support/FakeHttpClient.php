<?php

namespace Bluetech\Sdk\Tests\Support;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FakeHttpClient implements ClientInterface
{
    /** @var ResponseInterface[] */
    private array $queue;
    /** @var RequestInterface[] */
    public array $requests = [];

    public function __construct(array $responses)
    {
        $this->queue = $responses;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        if (count($this->queue) === 0) {
            throw new \RuntimeException('No queued response for request.');
        }
        return array_shift($this->queue);
    }
}
