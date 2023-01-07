<?php

namespace Modules\V1\Uploaders\Models\Constants;

enum FileMaxSize: int
{
    case MAX_SIZE_IN_KB = 20000; // 20Mb
}
