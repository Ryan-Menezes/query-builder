<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\NumberValue;

class NumberValueTest extends TestCase
{
    public function testShouldReturnAFormattedIntegerForASqlStatement()
    {
        $numberValue = new NumberValue(5);

        $expected = '5';

        $this->assertEquals($expected, $numberValue);
    }

    public function testShouldReturnAFormattedFloatForASqlStatement()
    {
        $numberValue = new NumberValue(5.5);

        $expected = '5.5';

        $this->assertEquals($expected, $numberValue);
    }
}
