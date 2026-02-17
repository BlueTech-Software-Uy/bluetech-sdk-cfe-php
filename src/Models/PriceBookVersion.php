<?php

namespace Bluetech\Sdk\Models;

class PriceBookVersion extends Model
{
    public int $id;
    public int $priceBookId;
    public int $version;
    public ?string $createdAt = null;
    public ?string $effectiveFrom = null;
    /** @var PriceBookItem[] */
    public array $items = [];
}
