<?php

namespace Bluetech\Sdk\Models;

class PriceBookVersionsResponse extends Model
{
    /** @var PriceBookVersion[] */
    public array $data = [];
    public ?Pagination $pagination = null;
}
