<?php

namespace Modules\V1\Meetings\Models\Pipelines\Accumulator;

use Modules\V1\Meetings\Models\Pipelines\Contracts\BasePipeline;

class Accumulator extends BasePipeline
{
    /**
     * Step responsibility is to reduce the size of given array and merge users data into one array item
     * Note:
     * There are two formats for each item:
     * 1- external_user_id and full_name WITHOUT any specific time
     * 2- external_user_id and 2 dates and a unique ID.
     *
     * No Specific extra validation will be applied for give data.
     */

    /**
     * @param          $data
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($data, \Closure $next): mixed
    {
        $mergedEmployeeData = $this->accumulateEmployeeData($data);

        return $next($mergedEmployeeData);
    }

    private function accumulateEmployeeData(array $employeeBusyTimes): array
    {
        /*
         * Array reduce is much faster than
         */
        return array_reduce($employeeBusyTimes, function ($accumulator, $item) {
            /**
             * Item[0] is user id and will be set on array key.
             */
            $index = $item[0];
            $itemCount = count($item);

            switch ($itemCount) {
                /*
                 * First pattern only has user id and user full_name
                 */
                case 2:
                    $accumulator[$index]['full_name'] = $item[1] ?? null;
                    break;
                    /*
                     * second pattern does not have full_name
                     */
                case 4:
                    $accumulator[$index]['external_user_id'] = $item[0];
                    $accumulator[$index]['full_name'] = null;
                    $accumulator[$index]['busy_times'][] =
                        isset($item[1], $item[2]) ? [$item[1], $item[2]] :
                            null;
                    $accumulator[$index]['external_unique_id'] = $item[3] ?? null;
                    break;
            }

            return $accumulator;
        }, []);
    }
}
