<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\NumberValue;

/**
 * @requires PHP 8.1
 */
class NumberValueTest extends TestCase
{
    public function testShouldReturnAFormattedPositiveIntegerForASqlStatement()
    {
        $numberValue = new NumberValue(5);

        $expected = '5';

        $this->assertEquals($expected, $numberValue);
    }

    public function testShouldReturnAFormattedPositiveFloatForASqlStatement()
    {
        $numberValue = new NumberValue(5.5);

        $expected = '5.5';

        $this->assertEquals($expected, $numberValue);
    }

    public function testShouldReturnAFormattedNegativeIntegerForASqlStatement()
    {
        $numberValue = new NumberValue(-5);

        $expected = '-5';

        $this->assertEquals($expected, $numberValue);
    }

    public function testShouldReturnAFormattedNegativeFloatForASqlStatement()
    {
        $numberValue = new NumberValue(-5.5);

        $expected = '-5.5';

        $this->assertEquals($expected, $numberValue);
    }
}
