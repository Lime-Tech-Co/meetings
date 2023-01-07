<?php

namespace Modules\V1\Uploaders\Services\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\V1\Uploaders\Services\Upload;
use Str;

class UploadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Upload::class, function () {
            $activeUploader = config('filesystems.default');

            $uploaderClass = '\\Modules\\V1\\Notifier\\TextMessage\\Publishers\\' . Str::studly($activeUploader);

            return new $uploaderClass();
        });
    }
}
