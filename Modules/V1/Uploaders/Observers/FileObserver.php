<?php

namespace Modules\V1\Uploaders\Observers;

use Modules\V1\Meetings\Commands\Importer\EmployeeBusyTimeImporter;

class FileObserver
{
    /**
     * Handle the File "created" event.
     *
     * @return void
     */
    public function created(): void
    {
        \Artisan::call(EmployeeBusyTimeImporter::class);
    }
}
