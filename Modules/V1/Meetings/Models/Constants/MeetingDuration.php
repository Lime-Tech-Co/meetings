<?php

namespace Modules\V1\Meetings\Models\Constants;

enum MeetingDuration: int
{
    /*
     * 60 Minutes
     */
    case MINIMUM_MEETING_DURATION = 30;
    case MAXIMUM_MEETING_DURATION = 120;
}
