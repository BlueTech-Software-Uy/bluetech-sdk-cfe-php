<?php

namespace Bluetech\Sdk\Models;

class PriceBookCreateRequest extends Model
{
    public string $name;
    public string $currency;
    public ?string $description = null;
    public ?string $effectiveFrom = null;
    /** @var PriceBookItem[] */
    public array $items = [];
}
