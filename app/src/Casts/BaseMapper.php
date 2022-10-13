<?php

namespace App\Casts;

use App\Casts\Contracts\Castable;

abstract class BaseMapper
{
    /**
     * @return array<string, \App\Casts\Contracts\Castable>
     */
    abstract public function types(): array;

    /**
     * @param string $alias
     * @return \App\Casts\Contracts\Castable|null
     */
    private function findType(string $alias): Castable|null
    {
        return $this->types()[$alias] ?? null;
    }

    /**
     * @param string $alias
     * @param mixed $value
     * @return mixed
     */
    public function get(string $alias, mixed $value): mixed
    {
        /** @var \App\Casts\Contracts\Castable $type */
        $type = $this->findType($alias);

        if ($type === null)
            throw new \InvalidArgumentException("Type {$alias} not found");

        return $type->unpack($value);
    }

    /**
     * @param string $alias
     * @param mixed $value
     * @return mixed
     */
    public function set(string $alias, mixed $value): mixed
    {
        /** @var \App\Casts\Contracts\Castable $type */
        $type = $this->findType($alias);

        if ($type === null)
            throw new \InvalidArgumentException("Type {$alias} not found");

        return $type->pack($value);
    }
}
