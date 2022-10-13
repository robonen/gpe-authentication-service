<?php

namespace App\Casts\Types;

use App\Casts\Contracts\Castable;

class AsEnum implements Castable
{
    private $enumClass;

    public function __construct($enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function typeName(): string
    {
        return 'enum';
    }

    public function pack(mixed $value)
    {
        if (!$value instanceof \BackedEnum)
            throw new \InvalidArgumentException("Value must be instance of enum with scalar backing type");

        if (!$value instanceof $this->enumClass)
            throw new \InvalidArgumentException("Value must be an instance of {$this->enumClass}");

        return (string) $value->value;
    }

    public function unpack(mixed $value)
    {
        return $this->enumClass::from($value);
    }
}
