<?php

namespace Modules\V1\Meetings\Tests\Unit;

use Modules\V1\Meetings\Models\Pipelines\Transformer\Transformer;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    public const FILE_PATH = __DIR__.'/Files/freebusy.txt';

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testIfContentCanBeTransformedIntoArray(): void
    {
        $data = file_get_contents(self::FILE_PATH);

        /**
         * convertToArray method is private, therefore we need to change accessibility.
         */
        $transformerClass = new Transformer();
        $reflection = new \ReflectionClass(Transformer::class);

        $method = $reflection->getMethod('convertToArray');
        $method->setAccessible(true);

        $contents = $method->invokeArgs($transformerClass, [$data]);

        $this->assertIsArray($contents);
    }
}
