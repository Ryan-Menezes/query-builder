<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Factories\FieldFactory;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Operators\Logical\Where;

trait HasWhere
{
    private Where $where;

    public function where(string $column, string $operator, mixed $value): self
    {
        $field = FieldFactory::createField($column, $operator, $value);
        $this->where->and($field);

        return $this;
    }

    public function orWhere(
        string $column,
        string $operator,
        mixed $value,
    ): self {
        $field = FieldFactory::createField($column, $operator, $value);
        $this->where->or($field);

        return $this;
    }

    public function whereBetween(string $columnName, array $values): self
    {
        $between = FieldFactory::createBetween($columnName, $values);

        $this->where->and($between);

        return $this;
    }

    public function orWhereBetween(string $columnName, array $values): self
    {
        $between = FieldFactory::createBetween($columnName, $values);

        $this->where->or($between);

        return $this;
    }

    public function whereNotBetween(string $columnName, array $values): self
    {
        $between = FieldFactory::createNotBetween($columnName, $values);

        $this->where->and($between);

        return $this;
    }

    public function orWhereNotBetween(string $columnName, array $values): self
    {
        $between = FieldFactory::createNotBetween($columnName, $values);

        $this->where->or($between);

        return $this;
    }

    public function whereBetweenColumns(string $columnName, array $values): self
    {
        [$firstColumn, $secondColumn] = $values;

        $between = FieldFactory::createBetween($columnName, [
            ValueFactory::createRawValue($firstColumn),
            ValueFactory::createRawValue($secondColumn),
        ]);

        $this->where->and($between);

        return $this;
    }

    public function orWhereBetweenColumns(
        string $columnName,
        array $values,
    ): self {
        [$firstColumn, $secondColumn] = $values;

        $between = FieldFactory::createBetween($columnName, [
            ValueFactory::createRawValue($firstColumn),
            ValueFactory::createRawValue($secondColumn),
        ]);

        $this->where->or($between);

        return $this;
    }

    public function whereNotBetweenColumns(
        string $columnName,
        array $values,
    ): self {
        [$firstColumn, $secondColumn] = $values;

        $between = FieldFactory::createNotBetween($columnName, [
            ValueFactory::createRawValue($firstColumn),
            ValueFactory::createRawValue($secondColumn),
        ]);

        $this->where->and($between);

        return $this;
    }

    public function orWhereNotBetweenColumns(
        string $columnName,
        array $values,
    ): self {
        [$firstColumn, $secondColumn] = $values;

        $between = FieldFactory::createNotBetween($columnName, [
            ValueFactory::createRawValue($firstColumn),
            ValueFactory::createRawValue($secondColumn),
        ]);

        $this->where->or($between);

        return $this;
    }
}
