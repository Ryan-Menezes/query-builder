<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Sql\Values\StringValue;
use Stringable;

/**
 * @requires PHP 8.1
 */
class StringValueTest extends TestCase
{
    private function makeSut(string|Stringable $value): ValueInterface
    {
        return new StringValue($value);
    }

    public function testShouldReturnAFormattedStringForASqlStatement()
    {
        $sut = $this->makeSut('any-string');

        $expected = '\'any-string\'';

        $this->assertEquals($expected, $sut->toSql());
    }
}
