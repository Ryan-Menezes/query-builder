<?php

namespace Tests\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Utils\Str;

/**
 * @requires PHP 8.1
 */
class StrTest extends TestCase
{
    /**
     * @dataProvider shouldReturnEverythingAfterTheSpecifiedStringProvider
     */
    public function testShouldReturnEverythingAfterTheSpecifiedString(string $value, string $search, string $expected)
    {
        $string = new Str($value);

        $actual = $string->after($search);

        $this->assertEquals($expected, $actual);
    }

    public function shouldReturnEverythingAfterTheSpecifiedStringProvider()
    {
        return [
            ['any-string.after', '.', 'after'],
            ['any-string.....after', '.....', 'after'],
            ['any-string', '.', ''],
            ['any-string', '.....', ''],
            ['any-string.', '.', ''],
            ['any-string.....', '.....', ''],
        ];
    }

    /**
     * @dataProvider shouldReturnEverythingBeforeTheSpecifiedStringProvider
     */
    public function testShouldReturnEverythingBeforeTheSpecifiedString(string $value, string $search, string $expected)
    {
        $string = new Str($value);

        $actual = $string->before($search);

        $this->assertEquals($expected, $actual);
    }

    public function shouldReturnEverythingBeforeTheSpecifiedStringProvider()
    {
        return [
            ['before.any-string', '.', 'before'],
            ['before.....any-string', '.....', 'before'],
            ['any-string', '.', ''],
            ['any-string', '.....', ''],
            ['.any-string', '.', ''],
            ['.....any-string', '.....', ''],
        ];
    }
}
