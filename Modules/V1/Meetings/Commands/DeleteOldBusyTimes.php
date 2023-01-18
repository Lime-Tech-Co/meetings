<?php

namespace Modules\V1\Meetings\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Modules\V1\Meetings\Models\EmployeeBusyTime;

class DeleteOldBusyTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetings:delete-old-busy-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will remove old entries';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('start to get old busy times...');

        $oldBusyTimes = $this->getOldBusyTimes();

        if (0 === $oldBusyTimes->count()) {
            $this->info('No old entries found...');

            return;
        }

        $oldBusyTimes->map(function ($busyTime) {
            $this->info('record Id: '.$busyTime->id.' will be removed');
            $busyTime->delete();
        });

        $this->info('Done :)');
    }

    /**
     * @return Collection
     */
    private function getOldBusyTimes(): Collection
    {
        return EmployeeBusyTime::where('busy_at', '<', now())->get();
    }
}
