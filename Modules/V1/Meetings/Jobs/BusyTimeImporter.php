<?php

namespace Modules\V1\Meetings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\V1\Meetings\Models\EmployeeBusyTime;
use Modules\V1\Users\Jobs\NewUserRegistrar;

class BusyTimeImporter implements ShouldQueue
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
    public function __construct(public readonly array $busyTimes)
    {
        /*
         * Default Queue name changed.
         */
        $this->queue = 'busy_times_importer_queue';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (
            isset($this->busyTimes['external_user_id']) &&
            $this->busyTimes['should_user_register']
        ) {
            NewUserRegistrar::dispatch($this->busyTimes['external_user_id'], $this->busyTimes['full_name']);
        }

        try {
            foreach ($this->busyTimes['busy_times'] as $busyTime) {
                EmployeeBusyTime::create([
                    'external_user_id' => $this->busyTimes['external_user_id'],
                    'busy_at' => $busyTime,
                    'external_unique_id' => $this->busyTimes['external_unique_id'],
                ]);
            }
        } catch (\Exception $ex) {
            \Log::info('cannot import busy times!');
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
        \Log::info('cannot import busy time:'.$exception->getMessage());

        $this->release($this->retryAfter);
    }
}
