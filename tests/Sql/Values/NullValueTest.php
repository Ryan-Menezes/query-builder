<?php

namespace Tests\Sql\Values;

use QueryBuilder\Interfaces\ValueInterface;
use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\NullValue;

/**
 * @requires PHP 8.1
 */
class NullValueTest extends TestCase
{
    private function makeSut(): ValueInterface
    {
        return new NullValue();
    }

    public function testShouldReturnAnUnformattedRawValueToASqlStatement()
    {
        $sut = $this->makeSut();

        $expected = 'NULL';

        $this->assertEquals($expected, $sut);
    }
}
