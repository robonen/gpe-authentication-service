<?php

namespace App\Casts;

use App\Casts\Types\AsString;

final class ProjectSettingsMapper extends BaseMapper
{
    /**
     * @inheritDoc
     */
    public function types(): array
    {
        return [
            'AUTH_TYPE' => AsString::class,
        ];
    }
}
