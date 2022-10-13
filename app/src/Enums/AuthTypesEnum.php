<?php

namespace App\Enums;

use App\Enums\Traits\Arrayable;

enum AuthTypesEnum: string
{
    use Arrayable;

    case BASIC = 'basic';
    case MODERN = 'modern';
}
