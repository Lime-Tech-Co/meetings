<?php

namespace Modules\V1\Meetings\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\V1\Meetings\Models\Constants\CalendarInterval;
use Modules\V1\Meetings\Models\Constants\MeetingCacheKeys;
use Modules\V1\Meetings\Models\Constants\StandardTimeFormat;
use Modules\V1\Users\Models\User;

final class MeetingGenerator
{
    private array $workingHourTimeResolution;
    private int $meetingLength;
    private int $requestedDateFrom;
    private int $requestedDateTo;
    private Collection $participants;
    private string $tempCacheKey;
    private const RANDOM_STRING_LENGTH = 10;

    public function make(): array
    {
        $this->tempCacheKey = $this->getTemporarilyCacheKey();

        return $this->generateAvailableMeetingTimes();
    }

    /**
     * @return array
     */
    private function getWorkingHourTimeResolution(): array
    {
        return $this->workingHourTimeResolution;
    }

    /**
     * @param array $workingHourTimeResolution
     */
    public function setWorkingHourTimeResolution(array $workingHourTimeResolution): void
    {
        $this->workingHourTimeResolution = $workingHourTimeResolution;
    }

    /**
     * @return int
     */
    private function getMeetingLength(): int
    {
        return $this->meetingLength * CalendarInterval::DEFAULT_INTERVAL->value;
    }

    /**
     * @param int $meetingLength
     */
    public function setMeetingLength(int $meetingLength): void
    {
        $this->meetingLength = $meetingLength;
    }

    /**
     * @return int
     */
    private function getRequestedDateFrom(): int
    {
        return $this->requestedDateFrom;
    }

    /**
     * @param int $requestedDateFrom
     */
    public function setRequestedDateFrom(int $requestedDateFrom): void
    {
        $this->requestedDateFrom = $requestedDateFrom;
    }

    /**
     * @return int
     */
    private function getRequestedDateTo(): int
    {
        return $this->requestedDateTo;
    }

    /**
     * @param int $requestedDateTo
     */
    public function setRequestedDateTo(int $requestedDateTo): void
    {
        $this->requestedDateTo = $requestedDateTo;
    }

    /**
     * @return Collection
     */
    private function getParticipants(): Collection
    {
        return $this->participants;
    }

    /**
     * @param Collection $participants
     */
    public function setParticipants(Collection $participants): void
    {
        $this->participants = $participants;
    }

    /**
     * @return array
     */
    private function generateAvailableMeetingTimes(): array
    {
        $result = [];
        $meetingParticipants = $this->getParticipants();

        $result[] = $meetingParticipants->map(function ($employee) {
            return [
                'employee' => [
                    'id' => $employee->external_user_id,
                    'full_name' => $employee->fullname ?? null,
                ],
                'availabilities' => $this->getUserAvailabilities($employee),
            ];
        });
        $this->flushCache();

        return $result;
    }

    /**
     * @param User $employee
     *
     * @return array
     */
    private function getUserAvailabilities(User $employee): array
    {
        $meetingCalendarTimes =
            Cache::has($this->tempCacheKey) ? Cache::get($this->tempCacheKey) : $this->generateCalendarTimes();
        $employeeBusyTimes = $employee->busyTimes->pluck('busy_at')->toArray();

        return array_reduce($meetingCalendarTimes, function ($result, $meetingDate) use ($employeeBusyTimes) {
            if (
                !in_array($meetingDate['start_at'], $employeeBusyTimes, true)
                && !in_array($meetingDate['finished_at'], $employeeBusyTimes, true)
            ) {
                $result[] = $meetingDate;
            }

            return $result;
        });
    }

    /**
     * @return array
     */
    private function generateCalendarTimes(): array
    {
        /**
         * The below times are converted to timestamp.
         */
        $startsAt = $this->getRequestedDateFrom();
        $finishedAt = $this->getRequestedDateTo();

        return Cache::remember(
            $this->tempCacheKey,
            MeetingCacheKeys::GENERATED_CALENDAR_TIME,
            function () use ($startsAt, $finishedAt) {
                $result = [];
                /**
                 * 1- $i will be $startsAt, and it would be less or equal finishedAt
                 * 2- As "i" is the time stamp, in order to generate the time resolution
                 *  meetingLength will be added to "i"
                 *  example:
                 *  MeetingLength => 120 Minutes * 60 seconds = 7200 seconds
                 *  StartsAd => 1675324800
                 *  FinishedAt  => 1675368000
                 *  MeetingLength would be added to "i" for each iteration.
                 */
                for ($i = $startsAt; $i <= $finishedAt; $i += $this->getMeetingLength()) {
                    $meetingDateTime = date(StandardTimeFormat::DEFAULT->value, $i);

                    $minute = Carbon::parse($meetingDateTime)->minute;
                    $hour = Carbon::parse($meetingDateTime)->hour;

                    if (
                        $hour >= $this->getWorkingHourTimeResolution()['started_at'] &&
                        $hour < $this->getWorkingHourTimeResolution()['finished_at']
                    ) {
                        $startMinuteAt = match (true) {
                            ($minute >= 15 && $minute <= 30) => 30,
                            default => 0,
                        };

                        $result[] = Carbon::parse($meetingDateTime)->setTime($hour, $startMinuteAt)->format(
                            StandardTimeFormat::DEFAULT->value
                        );
                    }
                }

                /*
                 * Example:
                 * Generated calendar
                 * [
                 *  "2023-02-02 08:00:00"
                 *  "2023-02-02 10:00:00"
                 *  "2023-02-02 12:00:00"
                 *  "2023-02-02 14:00:00"
                 *  "2023-02-02 16:00:00"
                 *  ]
                 */

                return $this->groupGeneratedDateTimes($result);
            }
        );
    }

    private function getTemporarilyCacheKey(): string
    {
        return implode('-', [
            MeetingCacheKeys::GENERATED_CALENDAR,
            Str::random(self::RANDOM_STRING_LENGTH),
        ]);
    }

    private function flushCache(): void
    {
        Cache::delete($this->tempCacheKey);
    }

    /**
     * @param array $times
     *
     * @return array
     */
    private function groupGeneratedDateTimes(array $times): array
    {
        /*
         * $times would be:
         * [
         *  "2023-02-02 08:00:00"
         *  "2023-02-02 10:00:00"
         *  "2023-02-02 12:00:00"
         *  "2023-02-02 14:00:00"
         *  "2023-02-02 16:00:00"
         *  ]
         *
         * Result after Manipulation:
         * [
         *  [
         *      "start_at" => "2023-02-02 08:00:00",
         *      "finished_at" => "2023-02-02 10:00:00",
         *  ],
         *  [
         *      "start_at" => "2023-02-02 10:00:00",
         *      "finished_at" => "2023-02-02 12:00:00",
         *  ],
         *  [
         *      "start_at" => "2023-02-02 12:00:00",
         *      "finished_at" => "2023-02-02 14:00:00",
         *  ],
         *  [
         *      "start_at" => "2023-02-02 14:00:00",
         *      "finished_at" => "2023-02-02 16:00:00",
         *  ],
         * ]
         */
        $newAvailabilities = [];
        $generatedTimesCount = count($times);

        for ($i = 0; $i < $generatedTimesCount; ++$i) {
            $pair = array_slice($times, $i, 2); // Based on $i 0 and 1 index will be fetched
            if (isset($pair[0], $pair[1])) {
                $newAvailabilities[] = [
                    'start_at' => $pair[0],
                    'finished_at' => $pair[1],
                ];
            }
        }

        return $newAvailabilities;
    }
}
