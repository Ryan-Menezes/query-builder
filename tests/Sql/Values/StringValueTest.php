<?php

namespace Tests\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\StringValue;

class StringValueTest extends TestCase
{
    public function testShouldReturnAFormattedStringForASqlStatement()
    {
        $stringValue = new StringValue('any-string');

        $this->assertEquals('\'any-string\'', $stringValue);
    }
}
