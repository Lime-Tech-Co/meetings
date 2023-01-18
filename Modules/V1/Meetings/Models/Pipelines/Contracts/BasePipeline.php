<?php

namespace Modules\V1\Meetings\Models\Pipelines\Contracts;

abstract class BasePipeline
{
    abstract public function handle($data, \Closure $next);
}
