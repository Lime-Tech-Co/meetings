<?php

namespace Modules\V1\Uploaders\Services\Uploaders\Local;

use Modules\V1\Uploaders\Services\Upload;
use Modules\V1\Uploaders\Models\Constants\Folders;

class UploadToLocal extends Upload
{
    public function doUpload($file): string
    {
        return $file->store(Folders::DEFAULT_FOLDER->value);
    }
}