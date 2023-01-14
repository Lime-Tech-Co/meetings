<?php

namespace Modules\V1\Users\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\V1\Users\Models\User;

class NewUserRegistrar implements ShouldQueue
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
    public function __construct(public readonly string $externalUserId, public readonly ?string $userFullName)
    {
        /*
         * Default Queue name changed.
         */
        $this->queue = 'user_registration_queue';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $user = new User();
            $user->external_user_id = $this->externalUserId;
            $user->full_name = $this->userFullName ?? null;

            $user->save();
        } catch (\Exception $ex) {
            \Log::info($ex->getMessage());
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
