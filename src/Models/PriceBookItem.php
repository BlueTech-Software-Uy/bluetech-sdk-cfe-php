<?php

namespace Bluetech\Sdk\Models;

class PriceBookItem extends Model
{
    public string $sku;
    public ?string $name = null;
    public float $price;
    public ?string $unit = null;
    public $taxRate = null;
}
