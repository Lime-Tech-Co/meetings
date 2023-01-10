<?php

namespace Modules\V1\Meetings\Models\Pipelines\Reviser;

use Closure;
use Carbon\Carbon;
use Modules\V1\Users\Models\User;
use Modules\V1\Users\Models\Constants\UserStatus;
use Modules\V1\Meetings\Models\Pipelines\Contracts\BasePipeline;

class Reviser extends BasePipeline
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
        $this->activeUsersId = $this->getActiveUserIds();
        $validatedEmployeeData = $this->validateEmployeeBusyDates($data);

        return $next($validatedEmployeeData);
    }

    /**
     * @param array $employeeBusyTimes
     *
     * @return array
     */
    private function validateEmployeeBusyDates(array $employeeBusyTimes): array
    {
        foreach ($employeeBusyTimes as $userId => $items) {
            /**
             * Users data must be kept in database (user id in fact)
             */
            $employeeBusyTimes[$userId]['should_user_register'] = $this->shouldUserRegister($userId);
            $employeeBusyTimes[$userId]['busy_times'] = $this->validateDates($items['busy_times']);
        }

        return $employeeBusyTimes;
    }

    /**
     * @return array
     */
    private function getActiveUserIds(): array
    {
        return User::select('id')
                   ->where('status', UserStatus::ENABLED->value)
                   ->get()
                   ->toArray();
    }

    /**
     * @param int|string $userId
     *
     * @return bool
     */
    private function shouldUserRegister(int|string $userId): bool
    {
        return !in_array($userId, $this->activeUsersId, true);
    }

    /**
     * @param array $busyTimes
     *
     * @return array|null
     */
    private function validateDates(array $busyTimes): array|null
    {
        return array_reduce($busyTimes, function ($accumulator, $items) {
            $dates = $this->dateParser($items);

            if (isset($dates[0]) && $dates[0] > now()) {
                $accumulator[] = $dates[0];
            }

            if (isset($dates[1]) && $dates[1] > now()) {
                $accumulator[] = $dates[1];
            }
            return $accumulator;
        });
    }

    /**
     * @param array $dates
     *
     * @return array
     */
    private function dateParser(array $dates): array
    {
        $result = [];
        foreach ($dates as $date) {
            $result[] = Carbon::parse($date)->toDateTimeString();
        }

        return $result;
    }
}