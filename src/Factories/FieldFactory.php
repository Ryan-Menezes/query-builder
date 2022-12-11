<?php

declare(strict_types=1);

namespace QueryBuilder\Factories;

use QueryBuilder\Interfaces\FieldInterface;
use QueryBuilder\Sql\Field;

abstract class FieldFactory
{
    public static function createField(
        string $column,
        string $operator,
        mixed $value,
    ): FieldInterface {
        $value = ValueFactory::createValue($value);

        return new Field($column, $operator, $value);
    }

    public static function createFieldWithRawValue(
        string $column,
        string $operator,
        string $value,
    ): FieldInterface {
        $value = ValueFactory::createRawValue($value);

        return new Field($column, $operator, $value);
    }

    public static function createFieldOnlyWithColumns(
        string $column,
        string $operator,
        string $value,
    ): FieldInterface {
        $value = ValueFactory::createRawValue($value);

        return new Field($column, $operator, $value);
    }
}
