<?php

namespace Modules\V1\Users\Models\Constants;

enum UserStatus: int
{
    case ENABLED = 1;
    case DISABLED = 0;

    /**
     * @return array
     */
    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
