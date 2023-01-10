<?php

namespace Modules\V1\Meetings\Models\Pipelines\Contracts;

use Closure;

abstract class BasePipeline
{
    abstract public function handle($data, Closure $next);
}