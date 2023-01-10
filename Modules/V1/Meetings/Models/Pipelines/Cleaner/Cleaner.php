<?php

namespace Modules\V1\Meetings\Models\Pipelines\Cleaner;

use Closure;
use Modules\V1\Meetings\Models\Pipelines\Contracts\BasePipeline;

class Cleaner extends BasePipeline
{
    protected array $activeUsersId;

    /**
     * @param         $data
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($data, Closure $next): mixed
    {
        $result = $this->removeEmptyItems($data);

        return $next($result);
    }

    /**
     * @param array $busyDates
     *
     * @return array
     */
    private function removeEmptyItems(array $busyDates): array
    {
        return array_filter($busyDates, function ($item) {
            return $item['should_user_register'] === true || !empty($item['busy_times']);
        });
    }
}