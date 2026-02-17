<?php

namespace Bluetech\Sdk\Models;

class PriceBook extends Model
{
    public int $id;
    public string $name;
    public ?string $description = null;
    public string $currency;
    public ?string $effectiveFrom = null;
    public ?string $createdAt = null;
}
