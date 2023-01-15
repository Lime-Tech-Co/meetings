<?php

namespace Modules\V1\Meetings\Models\Constants;

enum CalendarInterval: int
{
    /*
     * 30 Minutes = 1800 Seconds
     */
    case DEFAULT_INTERVAL = 1800;
}
