<?php

namespace Bluetech\Sdk\Models;

class UsageQueryResponse extends Model
{
    public ?int $contractId = null;
    public ?string $start = null;
    public ?string $end = null;
    /** @var array<int,UsageAggregate>|null */
    public ?array $data = null;
    public ?array $pagination = null;
}
