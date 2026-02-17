<?php

namespace Bluetech\Sdk\Models;

class UsageStatusHistoryResponse extends Model
{
    public int $contractId;
    /** @var UsageStatusHistoryEntry[] */
    public array $data = [];
    public ?Pagination $pagination = null;
}
