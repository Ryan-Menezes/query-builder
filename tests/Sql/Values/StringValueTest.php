<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\StringValue;

/**
 * @requires PHP 8.1
 */
class StringValueTest extends TestCase
{
    public function testShouldReturnAFormattedStringForASqlStatement()
    {
        $stringValue = new StringValue('any-string');

        $expected = '\'any-string\'';

        $this->assertEquals($expected, $stringValue);
    }
}
