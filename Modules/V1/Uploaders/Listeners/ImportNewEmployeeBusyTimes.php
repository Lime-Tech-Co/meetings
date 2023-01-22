<?php

namespace Modules\V1\Uploaders\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\V1\Meetings\Models\Pipelines\PipelineManager;
use Modules\V1\Uploaders\Events\FileUploaded;

class ImportNewEmployeeBusyTimes implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'listeners';

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
        } catch (\Exception $ex) {
            \Log::info(
                'cannot send file to importer pipeline: Id: '.$event->file->id.'because: '.$ex->getMessage()
            );
        }
    }
}
