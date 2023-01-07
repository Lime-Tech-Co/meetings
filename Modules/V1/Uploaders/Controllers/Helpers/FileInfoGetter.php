<?php

namespace Modules\V1\Uploaders\Controllers\Helpers;

class FileInfoGetter
{
    public static function extractFileInfos($file): array
    {
        return [
            'mimeType'  => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'size'      => $file->getSize(),
            'filename'  => $file->hashName(),
        ];
    }
}
