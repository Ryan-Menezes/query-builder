<?php

namespace Tests\Sql\Values;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\{
    BooleanValue,
    CollectionValue,
    NullValue,
    NumberValue,
    RawValue,
    StringValue,
};
use QueryBuilder\Exceptions\InvalidArgumentValueException;

/**
 * @requires PHP 8.1
 */
class CollectionValueTest extends TestCase
{
    public function testShouldReturnAFormattedStringForASqlStatement()
    {
        $collectionValue = new CollectionValue([
            true,
            null,
            5,
            new RawValue('NOW()'),
            'any-string',
            new RawValue('any-column'),
        ]);

        $this->assertEquals(
            '(?, ?, ?, NOW(), ?, any-column)',
            $collectionValue,
        );
        $this->assertEquals(
            [
                new BooleanValue(true),
                new NullValue(),
                new NumberValue(5),
                new RawValue('NOW()'),
                new StringValue('any-string'),
                new RawValue('any-column'),
            ],
            $collectionValue->getValue(),
        );
    }

    public function shouldThrowAnErrorIfAnArrayIsPassedTheCollectionOfValues()
    {
        $this->expectException(InvalidArgumentValueException::class);
        $this->expectExceptionMessage(
            'Arrays are not accepted in the value collection.',
        );

        new CollectionValue([[]]);
    }
}
