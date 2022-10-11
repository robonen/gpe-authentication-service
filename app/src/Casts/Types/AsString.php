<?php

namespace App\Casts\Types;

use App\Casts\Contracts\Castable;

class AsString implements Castable
{
    public function typeName(): string
    {
        return 'string';
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function pack(mixed $value): string
    {
        return (string) $value;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function unpack(mixed $value): string
    {
        return $value;
    }
}
