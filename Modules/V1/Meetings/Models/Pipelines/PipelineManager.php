<?php

namespace Modules\V1\Meetings\Models\Pipelines;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Storage;
use Modules\V1\Meetings\Jobs\BusyTimeImporter;
use Modules\V1\Meetings\Models\Pipelines\Accumulator\Accumulator;
use Modules\V1\Meetings\Models\Pipelines\Cleaner\Cleaner;
use Modules\V1\Meetings\Models\Pipelines\Contracts\ManagerInterface;
use Modules\V1\Meetings\Models\Pipelines\Reviser\Reviser;
use Modules\V1\Meetings\Models\Pipelines\Transformer\Transformer;
use Modules\V1\Uploaders\Models\File;

class PipelineManager implements ManagerInterface
{
    public static function runPipeline(File $file): void
    {
        $defaultStorage = Storage::disk(config('filesystems.default'));
        if (!$defaultStorage->exists($file->path)) {
            $file->shouldDelete();

            return;
        }

        /*
         * Importing Data will be in a pipeline as below:
         * 1- First step is transformer (convert txt to array)
         * 2- Second step is Accumulator (merge users data into single array)
         * 3- Third step is Reviser (BusyTimes will be validated, also new users will be determined)
         * 4- Final step is Cleaner (empty array items will be removed from the given data).
         */
        app(Pipeline::class)
            ->send($defaultStorage->get($file->path))
            ->through([
                Transformer::class,
                Accumulator::class,
                Reviser::class,
                Cleaner::class,
            ])->then(function (array $usersBusyTimes) {
                if (count($usersBusyTimes) > 0) {
                    array_map(static function ($times) {
                        BusyTimeImporter::dispatch($times);
                    }, $usersBusyTimes);
                }
            });

        $file->shouldDelete();
    }
}
