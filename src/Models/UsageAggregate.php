<?php

namespace Bluetech\Sdk\Models;

class UsageAggregate extends Model
{
    public string $bucket;
    public string $metric;
    public string $unit;
    public float $quantity;
}
