<?php

namespace App\Casts;

use App\Casts\Types\AsEnum;
use App\Enums\AuthTypesEnum;

;

final class ProjectSettingsMapper extends BaseMapper
{
    /**
     * @inheritDoc
     */
    public function types(): array
    {
        return [
            'AUTH_TYPE' => new AsEnum(AuthTypesEnum::class),
        ];
    }
}
