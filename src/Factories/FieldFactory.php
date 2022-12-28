<?php

declare(strict_types=1);

namespace QueryBuilder\Factories;

use QueryBuilder\Interfaces\FieldInterface;
use QueryBuilder\Sql\Field;

abstract class FieldFactory
{
    public static function createField(
        string $columnName,
        string $operator,
        mixed $value,
    ): FieldInterface {
        $value = ValueFactory::createValue($value);

        return new Field($columnName, $operator, $value);
    }

    public static function createFieldWithRawValue(
        string $columnName,
        string $operator,
        string $value,
    ): FieldInterface {
        $value = ValueFactory::createRawValue($value);

        return new Field($columnName, $operator, $value);
    }

    public static function createFieldOnlyWithColumns(
        string $columnName,
        string $operator,
        string $value,
    ): FieldInterface {
        $value = ValueFactory::createRawValue($value);

        return new Field($columnName, $operator, $value);
    }
}
