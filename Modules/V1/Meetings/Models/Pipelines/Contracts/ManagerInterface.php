<?php

namespace Modules\V1\Meetings\Models\Pipelines\Contracts;

use Modules\V1\Uploaders\Models\File;

interface ManagerInterface
{
    public static function runPipeline(File $file): void;
}
