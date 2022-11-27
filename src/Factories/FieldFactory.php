<?php

declare(strict_types=1);

namespace QueryBuilder\Factories;

use QueryBuilder\Interfaces\FieldInterface;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\{
    Field,
    Column,
};

abstract class FieldFactory
{
    public static function createField(string $column, string $operator, mixed $value): FieldInterface
    {
        $column = new Column($column);
        $value = ValueFactory::createValue($value);

        return new Field($column, $operator, $value);
    }

    public static function createFieldWithRawValue(string $column, string $operator, string $value): FieldInterface
    {
        $column = new Column($column);
        $value = new RawValue($value);

        return new Field($column, $operator, $value);
    }

    public static function createFieldOnlyWithColumns(string $column, string $operator, string $value): FieldInterface
    {
        $column = new Column($column);
        $value = ValueFactory::createRawValue(new Column($value));

        return new Field($column, $operator, $value);
    }
}
