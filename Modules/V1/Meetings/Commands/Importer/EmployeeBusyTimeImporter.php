<?php

namespace Modules\V1\Meetings\Commands\Importer;

use Illuminate\Console\Command;
use Modules\V1\Uploaders\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Filesystem\Filesystem;

class EmployeeBusyTimeImporter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetings:import-employees-busy-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will select uploaded new files and import them into Database';

    protected Filesystem $defaultStorage;

    public function __construct() {
        parent::__construct();
        $this->defaultStorage = Storage::disk(config('filesystems.default'));
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function handle(): void
    {

        /**
         * Steps:
         * 1- New files should be selected from files table.
         * 2- Each file should be checked if they are existed.
         *
         */
        $getNewFiles = $this->getNewFiles();

        if ($getNewFiles === null) {
            $this->info('There is no file...');
            return;
        }

        $getNewFiles->map(callback: function ($file) {
            $this->info("fileId: $file->id is going to be imported hopefully..");
            $this->importNewBusyTimes($file);
        });
    }

    private function getNewFiles(): Collection
    {
        return File::active()->get();
    }

    private function importNewBusyTimes(File $file): void
    {
        if (!$this->defaultStorage->exists($file->path)) {
            $this->info("fileId: $file->id is not EXISTS and will be removed.");
            $this->removeFile($file);
            return;
        }
        // TODO Importer Service will be called
        dd('importing logic.....');
    }

    private function removeFile(File $file): void {
        $file->should_delete = true;
        $file->save();
    }
}
