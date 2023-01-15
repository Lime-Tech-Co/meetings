<?php

namespace Modules\V1\Meetings\Tests\Unit;

use Carbon\Carbon;
use Modules\V1\Meetings\Models\Constants\StandardTimeFormat;
use PHPUnit\Framework\TestCase;

class ReviserTest extends TestCase
{
    public array $activeUsersId;
    public array $oldExternalUniqueIds;

    /**
     * @return void
     */
    public function testIfArrayCanBeCleaned(): void
    {
        $data = $this->accumulatedArray();
        $this->activeUsersId = $this->getActiveUserIds();
        $this->oldExternalUniqueIds = $this->getOldImportedExternalUniqueIds();

        $contents = $this->validateEmployeeBusyDates($data);
        $expectedResponse = [
            '57646786307395936680161735716561753784' => [
                'external_user_id' => '57646786307395936680161735716561753784',
                'full_name' => 'Farshid Boroomand',
                'busy_times' => null,
                'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
                'should_user_register' => true,
            ],
            '57646786307395936680161735716561753785' => [
                'external_user_id' => '57646786307395936680161735716561753785',
                'full_name' => null,
                'busy_times' => null,
                'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
                'should_user_register' => false,
            ],
        ];

        $this->assertEquals($contents, $expectedResponse);
    }

    /**
     * @return array[]
     */
    public function accumulatedArray(): array
    {
        return [
            '57646786307395936680161735716561753784' => [
                'external_user_id' => '57646786307395936680161735716561753784',
                'full_name' => 'Farshid Boroomand',
                'busy_times' => [
                    [
                        '3/13/2015 8:00:00 AM',
                        '3/13/2015 1:00:00 PM',
                    ],
                    [
                        '3/13/2015 9:00:00 AM',
                        '3/13/2015 2:00:00 PM',
                    ],
                ],
                'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
            ],
            '57646786307395936680161735716561753785' => [
                'external_user_id' => '57646786307395936680161735716561753785',
                'full_name' => null,
                'busy_times' => [
                    [
                        '3/13/2015 10:00:00 AM',
                        '3/13/2015 3:00:00 PM',
                    ],
                    [
                        '3/13/2015 11:00:00 AM',
                        '3/13/2015 4:00:00 PM',
                    ],
                ],
                'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
            ],
        ];
    }

    /**
     * @param array $employeeBusyTimes
     *
     * @return array
     */
    private function validateEmployeeBusyDates(array $employeeBusyTimes): array
    {
        foreach ($employeeBusyTimes as $userId => $items) {
            $employeeBusyTimes[$userId]['should_user_register'] = $this->shouldUserRegister($userId);
            $employeeBusyTimes[$userId]['busy_times'] = $this->validateDates($items['busy_times']);
            $employeeBusyTimes[$userId] = $this->removeDuplicateEntries($employeeBusyTimes[$userId]);
        }

        return $employeeBusyTimes;
    }

    /**
     * @return array
     */
    private function getActiveUserIds(): array
    {
        return [
            '57646786307395936680161735716561753785',
        ];
    }

    public function getOldImportedExternalUniqueIds(): array
    {
        return [
            'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D',
        ];
    }

    private function shouldUserRegister(int|string $userId): bool
    {
        return !in_array($userId, $this->activeUsersId, true);
    }

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

    private function dateParser(array $dates): array
    {
        $result = [];
        foreach ($dates as $date) {
            $result[] = Carbon::parse($date)->format(StandardTimeFormat::DEFAULT->value);
        }

        return $result;
    }

    private function removeDuplicateEntries(mixed $busyTimes)
    {
        if (in_array($busyTimes['external_unique_id'], $this->oldExternalUniqueIds, true)) {
            $busyTimes['busy_times'] = [];
        }

        return $busyTimes;
    }
}
