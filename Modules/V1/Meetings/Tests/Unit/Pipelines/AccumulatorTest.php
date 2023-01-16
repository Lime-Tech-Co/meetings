<?php

namespace Modules\V1\Meetings\Tests\Unit\Pipelines;

use PHPUnit\Framework\TestCase;
use Modules\V1\Meetings\Models\Pipelines\Accumulator\Accumulator;

class AccumulatorTest extends TestCase
{
    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testIfArrayCanBeAccumulated(): void
    {
        $data = $this->transformedArray();

        $accumulateClass = new Accumulator();
        $reflection = new \ReflectionClass(Accumulator::class);

        $method = $reflection->getMethod('accumulateEmployeeData');
        $method->setAccessible(true);

        $contents = $method->invokeArgs($accumulateClass, [$data]);

        $this->assertIsArray($contents);
        $this->assertArrayHasKey('57646786307395936680161735716561753784', $contents);
        $this->assertArrayHasKey('57646786307395936680161735716561753785', $contents);

        $this->assertArrayHasKey('busy_times', $contents['57646786307395936680161735716561753784']);
        $this->assertArrayHasKey('busy_times', $contents['57646786307395936680161735716561753785']);
    }

    /**
     * @return array[]
     */
    public function transformedArray(): array
    {
        return [
            [
                '57646786307395936680161735716561753784',
                '3/13/2015 8:00:00 AM',
                '3/13/2015 1:00:00 PM',
                'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
            ],
            [
                '57646786307395936680161735716561753784',
                '3/13/2015 9:00:00 AM',
                '3/13/2015 2:00:00 PM',
                'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
            ],
            [
                '57646786307395936680161735716561753784',
                'Farshid Boroomand',
            ],
            [
                '57646786307395936680161735716561753785',
                '3/13/2015 10:00:00 AM',
                '3/13/2015 3:00:00 PM',
                'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
            ],
            [
                '57646786307395936680161735716561753785',
                '3/13/2015 11:00:00 AM',
                '3/13/2015 4:00:00 PM',
                'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
            ],
        ];
    }
}
