<?php

namespace Modules\V1\Uploaders\Services\Uploaders\Minio;

use Modules\V1\Uploaders\Services\Upload;

class UploadToMinio extends Upload
{
    public function doUpload($file): string
    {
        return 'path url from Minio service';
    }
}
