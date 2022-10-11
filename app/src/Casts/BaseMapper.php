<?php

namespace App\Casts;

abstract class BaseMapper
{
    /**
     * @return array<string, class-string<\App\Casts\Contracts\Castable>>
     */
    abstract public function types(): array;

    /**
     * @param string $alias
     * @return class-string<\App\Casts\Contracts\Castable>|null
     */
    private function findType(string $alias): string|null
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
        /** @var class-string<\App\Casts\Contracts\Castable> $type */
        $type = $this->findType($alias);

        if ($type === null)
            throw new \InvalidArgumentException("Type {$alias} not found");

        $caster = new $type();

        return $caster->unpack($value);
    }

    /**
     * @param string $alias
     * @param mixed $value
     * @return mixed
     */
    public function set(string $alias, mixed $value): mixed
    {
        /** @var class-string<\App\Casts\Contracts\Castable> $type */
        $type = $this->findType($alias);

        if ($type === null)
            throw new \InvalidArgumentException("Type {$alias} not found");

        $caster = new $type();

        return $caster->pack($value);
    }
}
