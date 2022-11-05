<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Exceptions\InvalidArgumentValueException;

class ValuesTest extends TestCase
{
    public function testShouldAcceptedValuesOfTypeString()
    {
        $values = new Values([
            'any-value',
        ]);

        $expected = '\'any-value\'';

        $this->assertEquals($expected, $values);
    }

    public function testShouldAcceptedValuesOfTypeNumber()
    {
        $values = new Values([
            5,
            5.5,
        ]);

        $expected = '5, 5.5';

        $this->assertEquals($expected, $values);
    }

    public function testShouldAcceptedValuesOfTypeBoolean()
    {
        $values = new Values([
            true,
            false,
        ]);

        $expected = '1, 0';

        $this->assertEquals($expected, $values);
    }

    public function testShouldAcceptedRawValues()
    {
        $values = new Values([
            new RawValue('COUNT(*)'),
        ]);

        $expected = 'COUNT(*)';

        $this->assertEquals($expected, $values);
    }

    /**
     * @dataProvider shouldThrowAnExceptionIfValueArrayHasAnyInvalidValueProvider
     */
    public function testShouldThrowAnExceptionIfValueArrayHasAnyInvalidValueProvider($invalidValues)
    {
        $this->expectException(InvalidArgumentValueException::class);

        new Values($invalidValues);
    }

    public function shouldThrowAnExceptionIfValueArrayHasAnyInvalidValueProvider()
    {
        return [
            [['']],             // Empty String
            [[new \StdClass]],  // Object
            [[null]],           // Null
            [[[]]],             // Array
            [[function(){}]],   // Callable
        ];
    }
}
