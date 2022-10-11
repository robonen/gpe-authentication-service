<?php

namespace App\Casts\Contracts;

interface Castable
{
    public function typeName(): string;

    public function pack(mixed $value);
    public function unpack(mixed $value);
}
