<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\NullValue;

/**
 * @requires PHP 8.1
 */
class NullValueTest extends TestCase
{
    public function testShouldReturnAnUnformattedRawValueToASqlStatement()
    {
        $rawValue = new NullValue();

        $expected = 'NULL';

        $this->assertEquals($expected, $rawValue);
    }
}
