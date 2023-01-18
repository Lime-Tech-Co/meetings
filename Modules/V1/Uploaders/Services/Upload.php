<?php

namespace Modules\V1\Uploaders\Services;

abstract class Upload
{
    abstract public function doUpload($file): string;
}
