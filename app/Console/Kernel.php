<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Modules\V1\Meetings\Commands\Cleaner\DeleteOldFiles;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\V1\Meetings\Commands\Importer\EmployeeBusyTimeImporter;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        EmployeeBusyTimeImporter::class,
        DeleteOldFiles::class,
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
         $schedule->command('meetings:delete-old-files')->everyThreeHours();
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
