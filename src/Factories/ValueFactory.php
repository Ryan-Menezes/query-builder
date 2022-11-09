<?php

declare(strict_types=1);

namespace QueryBuilder\Factories;

use QueryBuilder\Exceptions\InvalidArgumentValueException;
use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
    RawValue,
};

abstract class ValueFactory
{
    public static function createValue(mixed $item): ValueInterface
    {
        if($item instanceof ValueInterface) {
            return $item;
        }

        if(is_string($item)) {
            return new StringValue($item);
        }

        if(is_numeric($item)) {
            return new NumberValue($item);
        }

        if(is_bool($item)) {
            return new BooleanValue($item);
        }

        throw new InvalidArgumentValueException(
            'The value must be of type string, number or boolean, if you want to pass a value if formatting uses the ' . RawValue::class . ' class'
        );
    }
}
