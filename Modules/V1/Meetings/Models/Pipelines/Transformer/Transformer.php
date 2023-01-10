<?php

namespace Modules\V1\Meetings\Models\Pipelines\Transformer;

use Closure;
use Modules\V1\Meetings\Models\Pipelines\Contracts\BasePipeline;

class Transformer extends BasePipeline
{
    public function handle($data, Closure $next)
    {
        $transformedData = $this->convertToArray($data);

        return $next($transformedData);
    }

    /**
     * @param $content
     *
     * @return array
     */
    private function convertToArray($content): array
    {
        $result = explode(PHP_EOL, $content);

        return array_map(function ($item) {
            return explode(';', rtrim($item));
        }, $result);
    }
}
