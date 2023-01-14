<?php

namespace Modules\V1\Uploaders\Listeners;

use Modules\V1\Meetings\Models\Pipelines\PipelineManager;
use Modules\V1\Uploaders\Events\FileUploaded;

class ImportNewEmployeeBusyTimes
{
    /**
     * Handle the event.
     *
     * @param FileUploaded $event
     *
     * @return void
     */
    public function handle(FileUploaded $event): void
    {
        try {
            PipelineManager::runPipeline($event->file);
        } catch (\Exception $e) {
            \Log::info('cannot send file to importer pipeline: Id: '.$event->file->id);
        }
    }
}
