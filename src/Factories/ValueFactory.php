<?php

declare(strict_types=1);

namespace QueryBuilder\Factories;

use QueryBuilder\Exceptions\InvalidArgumentValueException;
use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
    NullValue,
    RawValue,
    CollectionValue,
};
use Stringable;

abstract class ValueFactory
{
    public static function createValue(mixed $value): ValueInterface
    {
        if ($value instanceof ValueInterface) {
            return $value;
        }

        if (is_string($value)) {
            return new StringValue($value);
        }

        if (is_numeric($value)) {
            return new NumberValue($value);
        }

        if (is_bool($value)) {
            return new BooleanValue($value);
        }

        if (is_null($value)) {
            return new NullValue();
        }

        if (is_array($value)) {
            return new CollectionValue($value);
        }

        throw new InvalidArgumentValueException(
            'The value must be of type string, number, boolean or array, if you want to pass a value if formatting uses the \QueryBuilder\Sql\Values\RawValue class',
        );
    }

    public static function createRawValue(
        string|Stringable $value,
    ): ValueInterface {
        return new RawValue($value);
    }

    public static function createCollectionValue(array $value): ValueInterface
    {
        return new CollectionValue($value);
    }
}
