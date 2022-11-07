<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;
use QueryBuilder\Sql\Values\BooleanValue;

/**
 * @requires PHP 8.1
 */
class BooleanValueTest extends TestCase
{
    public function testShouldReturnATrueAndValidBooleanValueForAnSqlStatement()
    {
        $booleanValue = new BooleanValue(true);

        $expected = '1';

        $this->assertEquals($expected, $booleanValue);
    }

    public function testShouldReturnAFalseAndValidBooleanValueForAnSqlStatement()
    {
        $booleanValue = new BooleanValue(false);

        $expected = '0';

        $this->assertEquals($expected, $booleanValue);
    }
}
