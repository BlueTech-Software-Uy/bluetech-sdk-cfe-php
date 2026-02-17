<?php

namespace Bluetech\Sdk\Models;

class PriceBookListResponse extends Model
{
    /** @var PriceBook[] */
    public array $data = [];
    public ?Pagination $pagination = null;
}
