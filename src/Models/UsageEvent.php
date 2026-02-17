<?php

namespace Bluetech\Sdk\Models;

class UsageEvent extends Model
{
    public int $contractId;
    public string $eventId;
    public string $occurredAt;
    public float $quantity;
    public string $unit;
    public string $metric;
    public ?string $description = null;
    public ?array $metadata = null;
}
