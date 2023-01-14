<?php

namespace Modules\V1\Uploaders\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Modules\V1\Uploaders\Models\File;

class DeleteFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;
    public int $retryAfter = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public File $file)
    {
        /*
         * Default Queue name changed.
         */
        $this->queue = 'delete_files_queue';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            /*
             * steps:
             * 1- File will be checked if it exists, then it will be removed physically.
             * 2- File record in DB will be removed.
             */
            if (Storage::disk(config('filesystems.default'))->exists($this->file->path)) {
                Storage::disk(config('filesystems.default'))->delete($this->file->path);
            }
            $this->file->delete();
        } catch (\Exception $ex) {
            $this->failed($ex);
        }
    }

    /**
     * Handle a job failure after specific seconds.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function failed(\Exception $exception): void
    {
        \Log::info('cannot delete file:'.$exception->getMessage());

        $this->release($this->retryAfter);
    }
}
