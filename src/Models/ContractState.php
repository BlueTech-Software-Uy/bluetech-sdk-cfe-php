<?php

namespace Bluetech\Sdk\Models;

class ContractState extends Model
{
    public int $contractId;
    public string $status;
    public ?string $nextInvoiceDate = null;
    public ?string $currency = null;
    public ?array $currentPeriod = null;
}
