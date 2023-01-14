<?php

namespace Modules\V1\Meetings\Tests\Unit;

use Modules\V1\Meetings\Models\Pipelines\Cleaner\Cleaner;
use PHPUnit\Framework\TestCase;

class CleanerTest extends TestCase
{
    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testIfArrayCanBeCleaned(): void
    {
        $data = $this->revisedArray();

        $cleanerClass = new Cleaner();
        $reflection = new \ReflectionClass(Cleaner::class);

        $method = $reflection->getMethod('removeEmptyItems');
        $method->setAccessible(true);

        $expectedResponse = [
            '57646786307395936680161735716561753784' => [
                'external_user_id' => '57646786307395936680161735716561753784',
                'full_name' => 'Farshid Boroomand',
                'busy_times' => null,
                'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7D..',
                'should_user_register' => true,
            ],
        ];

        $contents = $method->invokeArgs($cleanerClass, [$data]);

        $this->assertEquals($contents, $expectedResponse);
    }

    /**
     * @return array[]
     */
    public function revisedArray(): array
    {
        return [
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
    }
}
