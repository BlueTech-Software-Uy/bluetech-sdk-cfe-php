<?php

namespace Bluetech\Sdk\Models;

class UsageEventResponse extends Model
{
    public ?string $Res = null;
    public ?int $created = null;
    public ?int $ignored = null;
    /** @var array<int,UsageEvent>|null */
    public ?array $events = null;
}
