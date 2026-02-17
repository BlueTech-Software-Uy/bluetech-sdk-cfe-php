<?php

namespace Bluetech\Sdk\Models;

class UsageStatusHistoryEntry extends Model
{
    public ?string $statusFrom = null;
    public string $statusTo;
    /** @var string[] */
    public array $reasons = [];
    public ?string $createdAt = null;
}
