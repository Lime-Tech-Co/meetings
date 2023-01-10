<?php

namespace Modules\V1\Meetings\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\V1\Meetings\Models\Pipelines\Transformer\Transformer;

class TransformerTest extends TestCase
{
    public const FILE_PATH = __DIR__ . '/Files/freebusy.txt';

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function test_if_content_can_be_transformed_into_array(): void
    {
        $data = file_get_contents(self::FILE_PATH);

        /**
         * convertToArray method is private, therefore we need to change accessibility
         */
        $transformerClass = new Transformer();
        $reflection = new \ReflectionClass(Transformer::class);

        $method = $reflection->getMethod('convertToArray');
        $method->setAccessible(true);


        $contents = $method->invokeArgs($transformerClass, [$data]);

        $this->assertIsArray($contents);
    }
}