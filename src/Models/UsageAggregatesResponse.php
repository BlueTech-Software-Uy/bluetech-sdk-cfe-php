<?php

namespace Bluetech\Sdk\Models;

class UsageAggregatesResponse extends Model
{
    public int $contractId;
    public string $start;
    public string $end;
    /** @var UsageAggregate[] */
    public array $data = [];
    public ?Pagination $pagination = null;
}
