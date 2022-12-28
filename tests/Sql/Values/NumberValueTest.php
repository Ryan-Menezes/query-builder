<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Sql\Values\NumberValue;

/**
 * @requires PHP 8.1
 */
class NumberValueTest extends TestCase
{
    private function makeSut(int|float $value): ValueInterface
    {
        return new NumberValue($value);
    }

    public function testShouldReturnAFormattedPositiveIntegerForASqlStatement()
    {
        $sut = $this->makeSut(5);

        $expected = '5';

        $this->assertEquals($expected, $sut);
    }

    public function testShouldReturnAFormattedPositiveFloatForASqlStatement()
    {
        $sut = $this->makeSut(5.5);

        $expected = '5.5';

        $this->assertEquals($expected, $sut);
    }

    public function testShouldReturnAFormattedNegativeIntegerForASqlStatement()
    {
        $sut = $this->makeSut(-5);

        $expected = '-5';

        $this->assertEquals($expected, $sut);
    }

    public function testShouldReturnAFormattedNegativeFloatForASqlStatement()
    {
        $sut = $this->makeSut(-5.5);

        $expected = '-5.5';

        $this->assertEquals($expected, $sut);
    }
}
