<?php

namespace App\Enums\Traits;

trait Arrayable
{
    /**
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
