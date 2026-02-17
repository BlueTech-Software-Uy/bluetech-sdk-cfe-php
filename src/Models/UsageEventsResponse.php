<?php

namespace Bluetech\Sdk\Models;

class UsageEventsResponse extends Model
{
    public string $Res = 'OK';
    public int $created = 0;
    public int $ignored = 0;
    /** @var UsageEvent[] */
    public array $events = [];
}
