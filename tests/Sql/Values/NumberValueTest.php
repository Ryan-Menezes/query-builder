<?php

namespace Tests\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\NumberValue;

class NumberValueTest extends TestCase
{
    public function testShouldReturnAFormattedIntegerForASqlStatement()
    {
        $numberValue = new NumberValue(5);

        $this->assertEquals('5', $numberValue);
    }

    public function testShouldReturnAFormattedFloatForASqlStatement()
    {
        $numberValue = new NumberValue(5.5);

        $this->assertEquals('5.5', $numberValue);
    }
}
