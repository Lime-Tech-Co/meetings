<?php

namespace Modules\V1\Uploaders\Commands\Remover;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Modules\V1\Uploaders\Jobs\DeleteFile;
use Modules\V1\Uploaders\Models\File;

class DeleteOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:delete-old-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command would delete files which imported or not existed';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->getOldFiles()->map(callback: function ($file) {
            DeleteFile::dispatch($file);
        });
    }

    /**
     * @return Collection
     */
    private function getOldFiles(): Collection
    {
        return File::readyToDelete()->get();
    }
}
