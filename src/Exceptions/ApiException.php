<?php

namespace Bluetech\Sdk\Exceptions;

class ApiException extends \RuntimeException
{
    private int $statusCode;
    private ?string $errorCode;
    private array $details;
    private ?string $requestId;

    public function __construct(
        int $statusCode,
        string $message,
        ?string $errorCode = null,
        array $details = [],
        ?string $requestId = null
    ) {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->details = $details;
        $this->requestId = $requestId;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }
}
