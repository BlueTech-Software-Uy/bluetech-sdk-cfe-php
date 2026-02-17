<?php

namespace Bluetech\Sdk\Models;

abstract class Model
{
    public static function fromArray(array $data)
    {
        $obj = new static();
        foreach ($data as $key => $value) {
            if (property_exists($obj, $key)) {
                $obj->$key = $value;
            }
        }
        return $obj;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
