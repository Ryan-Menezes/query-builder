<?php

declare(strict_types=1);

namespace QueryBuilder\Factories;

use QueryBuilder\Interfaces\FieldInterface;
use QueryBuilder\Sql\Field;
use QueryBuilder\Sql\Operators\Comparators\Between;
use QueryBuilder\Sql\Operators\Comparators\In;

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

    public static function createBetween(
        string $columnName,
        array $values,
    ): FieldInterface {
        return new Between($columnName, $values);
    }

    public static function createNotBetween(
        string $columnName,
        array $values,
    ): FieldInterface {
        return (new Between($columnName, $values))->not();
    }

    public static function createIn(
        string $columnName,
        array $values,
    ): FieldInterface {
        return new In($columnName, $values);
    }

    public static function createNotIn(
        string $columnName,
        array $values,
    ): FieldInterface {
        return (new In($columnName, $values))->not();
    }
}
