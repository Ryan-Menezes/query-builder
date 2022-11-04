<?php

namespace Tests\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\RawValue;

class RawValueTest extends TestCase
{
    public function testShouldReturnAnUnformattedRawValueToASqlStatement()
    {
        $rawValue = new RawValue('any-value');

        $this->assertEquals('any-value', $rawValue);
    }
}
