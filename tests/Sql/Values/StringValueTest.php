<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\StringValue;

class StringValueTest extends TestCase
{
    public function testShouldReturnAFormattedStringForASqlStatement()
    {
        $stringValue = new StringValue('any-string');

        $expected = '\'any-string\'';

        $this->assertEquals($expected, $stringValue);
    }
}
