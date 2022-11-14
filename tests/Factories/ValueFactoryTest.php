<?php

namespace Tests\Factories;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Exceptions\InvalidArgumentValueException;
use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
    NullValue,
    RawValue,
};

/**
 * @requires PHP 8.1
 */
class ValueFactoryTest extends TestCase
{
    /**
     * @dataProvider shouldReturnTheClassCorrespondingToThatTypeProvider
     */
    public function testShouldReturnTheClassCorrespondingToThatType(mixed $value, ValueInterface $expected)
    {
        $actual = ValueFactory::createValue($value);

        $this->assertEquals($expected, $actual);
    }

    public function shouldReturnTheClassCorrespondingToThatTypeProvider()
    {
        return [
            ['any-string', new StringValue('any-string')],
            [5, new NumberValue(5)],
            [5.5, new NumberValue(5.5)],
            [true, new BooleanValue(true)],
            [null, new NullValue()],
            [new RawValue('any-raw'), new RawValue('any-raw')],
        ];
    }

    /**
     * @dataProvider shouldThrowAnExceptionIfValueIsInvalidProvider
     */
    public function testShouldThrowAnExceptionIfValueIsInvalid($invalidValue)
    {
        $this->expectException(InvalidArgumentValueException::class);

        ValueFactory::createValue($invalidValue);
    }

    public function shouldThrowAnExceptionIfValueIsInvalidProvider()
    {
        return [
            [new \StdClass],  // Object
            [[]],             // Array
            [function () {}], // Callable
        ];
    }
}
