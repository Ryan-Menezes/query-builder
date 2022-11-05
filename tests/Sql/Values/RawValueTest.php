<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\RawValue;

class RawValueTest extends TestCase
{
    public function testShouldReturnAnUnformattedRawValueToASqlStatement()
    {
        $rawValue = new RawValue('any-value');

        $expected = 'any-value';

        $this->assertEquals($expected, $rawValue);
    }
}
