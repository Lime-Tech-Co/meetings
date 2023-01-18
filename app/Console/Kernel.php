<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Modules\V1\Meetings\Commands\DeleteOldBusyTimes;
use Modules\V1\Uploaders\Commands\Remover\DeleteOldFiles;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        DeleteOldFiles::class,
        DeleteOldBusyTimes::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        /**
         * Old Files will be removed every three hours
         */
        $schedule->command('files:delete-old-files')->everyThreeHours();

        /**
         * Old employee busy times will be removed hourly
         */
        $schedule->command('meetings:delete-old-busy-times')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
