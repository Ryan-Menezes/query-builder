<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Sql\Values\RawValue;
use Stringable;

/**
 * @requires PHP 8.1
 */
class RawValueTest extends TestCase
{
    private function makeSut(string|Stringable $value): ValueInterface
    {
        return new RawValue($value);
    }

    public function testShouldReturnAnUnformattedRawValueToASqlStatement()
    {
        $sut = $this->makeSut('any-value');

        $expected = 'any-value';

        $this->assertEquals($expected, $sut);
    }
}
