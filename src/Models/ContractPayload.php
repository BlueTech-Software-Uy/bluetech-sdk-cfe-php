<?php

namespace Bluetech\Sdk\Models;

class ContractPayload extends Model
{
    /** @var array */
    public array $data = [];

    public static function fromArray(array $data)
    {
        $obj = new static();
        $obj->data = $data;
        return $obj;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
