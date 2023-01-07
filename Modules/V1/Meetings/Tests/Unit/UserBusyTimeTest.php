<?php

namespace Modules\V1\Meetings\Tests\Unit;

use PHPUnit\Framework\TestCase;

class UserBusyTimeTest extends TestCase
{
    public const FILE_PATH = __DIR__ . '/Files/freebusy.txt';

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_if_file_exists(): void
    {
        $contents = file_get_contents(self::FILE_PATH);
        $this->assertNotEmpty($contents);
        dd($this->mergeCommonArrays());
    }

    /**
     * @return array
     */
    public function convertFileToArray(): array
    {
        $data = file_get_contents(self::FILE_PATH);
        $result = explode(PHP_EOL, $data);

        return array_map(function ($item) {
            return explode(';', rtrim($item));
        }, $result);
    }

    public function mergeCommonArrays()
    {
        $result = [];

        foreach ($this->convertFileToArray() as $key => $item) {
            $result[$item[0]] = array_merge($result[$key] ?? [], $item);
        }
        dd($result);
    }
}