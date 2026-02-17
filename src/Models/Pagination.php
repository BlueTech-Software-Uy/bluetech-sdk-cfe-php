<?php

namespace Bluetech\Sdk\Models;

class Pagination extends Model
{
    public int $page = 1;
    public int $per_page = 50;
    public int $total = 0;
}
