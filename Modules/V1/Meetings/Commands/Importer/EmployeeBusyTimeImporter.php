<?php

namespace Modules\V1\Meetings\Commands\Importer;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Storage;
use Modules\V1\Meetings\Models\Pipelines\Accumulator\Accumulator;
use Modules\V1\Meetings\Models\Pipelines\Cleaner\Cleaner;
use Modules\V1\Meetings\Models\Pipelines\Reviser\Reviser;
use Modules\V1\Meetings\Models\Pipelines\Transformer\Transformer;
use Modules\V1\Uploaders\Models\File;

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

    public function __construct()
    {
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
         */
        $getNewFiles = $this->getNewFiles();

        if (null === $getNewFiles) {
            $this->info('There is no file...');

            return;
        }

        try {
            $getNewFiles->map(callback: function ($file) {
                $this->info("fileId: $file->id is going to be imported hopefully..");
                $this->importNewBusyTimes($file);
            });
        } catch (\Exception $ex) {
            \Log::info('Importing busy times failed'.$ex->getMessage());
        }
    }

    /**
     * @return Collection
     */
    private function getNewFiles(): Collection
    {
        return File::active()->get();
    }

    /**
     * @param File $file
     *
     * @return void
     */
    private function importNewBusyTimes(File $file): void
    {
        if (!$this->defaultStorage->exists($file->path)) {
            $this->info("fileId: $file->id is not EXISTS and will be removed.");
            $this->removeFile($file);

            return;
        }

        /**
         * Importing Data will be in a pipeline as below:
         * 1- First step is transformer (convert txt to array)
         * 2- Second step is Accumulator (merge users data into single array)
         * 3- Third step is Reviser (BusyTimes will be validated, also new users will be determined)
         * 4- Final step is Cleaner (empty array items will be removed from the given data).
         */
        $importingPipeline = app(Pipeline::class)
            ->send($this->defaultStorage->get($file->path))
            ->through([
                Transformer::class,
                Accumulator::class,
                Reviser::class,
                Cleaner::class,
            ])->then(function (array $times) {
                dd($times);
                // TODO: Importer Job will be dispatch here and MUST be queued
                // $this->removeFile($file);
            });

        $importingPipeline->run();
    }

    /**
     * @param File $file
     *
     * @return void
     */
    private function removeFile(File $file): void
    {
        $file->should_delete = true;
        $file->save();
    }
}
