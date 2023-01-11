<?php

namespace Modules\V1\Meetings\Models\Pipelines\Transformer;

use Modules\V1\Meetings\Models\Pipelines\Contracts\BasePipeline;

class Transformer extends BasePipeline
{
    /**
     * The input is a plain text which is separated by ";" (semicolon)
     * The text will be transformed into an array.
     */

    /**
     * @param          $data
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($data, \Closure $next): mixed
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
