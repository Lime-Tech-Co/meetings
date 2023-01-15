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
        return $this->meetingLength;
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
            if (!in_array($meetingDate, $employeeBusyTimes, true)) {
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
        $startsAt = $this->getRequestedDateFrom();
        $finishedAt = $this->getRequestedDateTo();

        return Cache::remember(
            $this->tempCacheKey,
            MeetingCacheKeys::GENERATED_CALENDAR_TIME,
            function () use ($startsAt, $finishedAt) {
                $result = [];
                for ($i = $startsAt; $i <= $finishedAt; $i += CalendarInterval::DEFAULT_INTERVAL->value) {
                    $meetingDateTime = date(StandardTimeFormat::DEFAULT->value, $i);
                    if (
                        Carbon::parse($meetingDateTime)->hour >= $this->getWorkingHourTimeResolution()['started_at'] &&
                        Carbon::parse(
                            $meetingDateTime
                        )->hour < $this->getWorkingHourTimeResolution()['finished_at']
                    ) {
                        $result[] = $meetingDateTime;
                    }
                }

                return $result;
            }
        );
    }

    private function getTemporarilyCacheKey(): string
    {
        return implode('-', [
            MeetingCacheKeys::GENERATED_CALENDAR,
            Str::random(10),
        ]);
    }

    private function flushCache(): void
    {
        Cache::delete($this->tempCacheKey);
    }
}
