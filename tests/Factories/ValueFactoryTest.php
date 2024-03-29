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
    CollectionValue,
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
    public function testShouldReturnTheClassCorrespondingToThatType(
        mixed $value,
        ValueInterface $expected,
    ) {
        $sut = ValueFactory::createValue($value);

        $this->assertEquals($expected, $sut);
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
            [[1, 2, 3], new CollectionValue([1, 2, 3])],
        ];
    }

    public function testShouldCreateARawValueClass()
    {
        $sut = ValueFactory::createRawValue('NOW()');
        $expected = new RawValue('NOW()');

        $this->assertEquals($expected, $sut);
    }

    public function testShouldCreateACollectionValueClass()
    {
        $values = ['any-value', 23, null];
        $sut = ValueFactory::createCollectionValue($values);
        $expected = new CollectionValue($values);

        $this->assertEquals($expected, $sut);
    }

    /**
     * @dataProvider shouldThrowAnExceptionIfValueIsInvalidProvider
     */
    public function testShouldThrowAnExceptionIfValueIsInvalid($invalidValue)
    {
        $this->expectException(InvalidArgumentValueException::class);
        $this->expectExceptionMessage(
            'The value must be of type string, number, boolean or array, if you want to pass a value if formatting uses the \QueryBuilder\Sql\Values\RawValue class',
        );

        ValueFactory::createValue($invalidValue);
    }

    public function shouldThrowAnExceptionIfValueIsInvalidProvider()
    {
        return [
            [new \StdClass()], // Object
            [function () {}], // Callable
        ];
    }
}
