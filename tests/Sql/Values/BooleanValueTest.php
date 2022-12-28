<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Sql\Values\BooleanValue;

/**
 * @requires PHP 8.1
 */
class BooleanValueTest extends TestCase
{
    private function makeSut(bool $value): ValueInterface
    {
        return new BooleanValue($value);
    }

    public function testShouldReturnATrueAndValidBooleanValueForAnSqlStatement()
    {
        $sut = $this->makeSut(true);

        $expected = '1';

        $this->assertEquals($expected, $sut);
    }

    public function testShouldReturnAFalseAndValidBooleanValueForAnSqlStatement()
    {
        $sut = $this->makeSut(false);

        $expected = '0';

        $this->assertEquals($expected, $sut);
    }
}
