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
use InvalidArgumentException;

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
        ]);

        $this->assertEquals('(?, ?, ?, NOW(), ?)', $collectionValue);
        $this->assertEquals([
            new BooleanValue(true),
            new NullValue(),
            new NumberValue(5),
            new RawValue('NOW()'),
            new StringValue('any-string'),
        ], $collectionValue->getValue());
    }

    public function shouldThrowAnErrorIfAnArrayIsPassedTheCollectionOfValues()
    {
        $this->expectException(InvalidArgumentException::class);

        new CollectionValue([
            [],
        ]);
    }
}
