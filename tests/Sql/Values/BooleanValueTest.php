<?php

namespace Tests\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\BooleanValue;

class BooleanValueTest extends TestCase
{
    public function testShouldReturnATrueAndValidBooleanValueForAnSqlStatement()
    {
        $booleanValue = new BooleanValue(true);

        $this->assertEquals('1', $booleanValue);
    }

    public function testShouldReturnAFalseAndValidBooleanValueForAnSqlStatement()
    {
        $booleanValue = new BooleanValue(false);

        $this->assertEquals('0', $booleanValue);
    }
}
