<?php

namespace Modules\V1\Uploaders\Observers;

use Modules\V1\Uploaders\Models\File;
use Modules\V1\Uploaders\Events\FileUploaded;

class FileObserver
{
    /**
     * Handle the File "created" event.
     *
     * @param File $file
     *
     * @return void
     */
    public function created(File $file): void
    {
        event(new FileUploaded($file));
    }
}
