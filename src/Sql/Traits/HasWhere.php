<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Exceptions\{
    InvalidArgumentArrayException,
    InvalidArgumentOperatorException,
};
use QueryBuilder\Factories\{FieldFactory, ValueFactory};
use QueryBuilder\Query;
use QueryBuilder\Sql\Operators\Logical\Where;

trait HasWhere
{
    private Where $where;

    public function where(
        array|string $column,
        mixed $operator = null,
        mixed $value = null,
    ): self {
        if (is_array($column)) {
            return $this->treatWheresArrayParam($column);
        }

        [$operator, $value] = $this->parseOperatorAndValue($operator, $value);

        $field = FieldFactory::createField($column, $operator, $value);
        $this->where->and($field);

        return $this;
    }

    public function orWhere(
        array|string $column,
        mixed $operator = null,
        mixed $value = null,
    ): self {
        if (is_array($column)) {
            return $this->treatOrWheresArrayParam($column);
        }

        [$operator, $value] = $this->parseOperatorAndValue($operator, $value);

        $field = FieldFactory::createField($column, $operator, $value);
        $this->where->or($field);

        return $this;
    }

    private function treatWheresArrayParam(array $fields): self
    {
        foreach ($fields as $params) {
            $this->throwErrorIfIsAnInvalidArrayParams($params);
            $this->where(...$params);
        }

        return $this;
    }

    private function treatOrWheresArrayParam(array $fields): self
    {
        foreach ($fields as $params) {
            $this->throwErrorIfIsAnInvalidArrayParams($params);
            $this->orWhere(...$params);
        }

        return $this;
    }

    private function throwErrorIfIsAnInvalidArrayParams(mixed $params): void
    {
        if (!is_array($params) || count($params) <= 1 || count($params) > 3) {
            throw new InvalidArgumentArrayException(
                'The first parameter should be of type string or array, and each element of the array must be another array with 2 or 3 elements.',
            );
        }
    }

    private function parseOperatorAndValue(mixed $operator, mixed $value): array
    {
        $this->throwIfIsAnInvalidOperator($operator);

        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }

        return [$operator, $value];
    }

    private function throwIfIsAnInvalidOperator(mixed $operator): void
    {
        if (is_null($operator)) {
            throw new InvalidArgumentOperatorException(
                'The operator must be a string of length greater than zero.',
            );
        }
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

    public function whereIn(string $columnName, array|Query $values): self
    {
        $values = $this->parseValues($values);
        $in = FieldFactory::createIn($columnName, $values);

        $this->where->and($in);

        return $this;
    }

    public function orWhereIn(string $columnName, array|Query $values): self
    {
        $values = $this->parseValues($values);
        $in = FieldFactory::createIn($columnName, $values);

        $this->where->or($in);

        return $this;
    }

    public function whereNotIn(string $columnName, array|Query $values): self
    {
        $values = $this->parseValues($values);
        $in = FieldFactory::createNotIn($columnName, $values);

        $this->where->and($in);

        return $this;
    }

    public function orWhereNotIn(string $columnName, array|Query $values): self
    {
        $values = $this->parseValues($values);
        $in = FieldFactory::createNotIn($columnName, $values);

        $this->where->or($in);

        return $this;
    }

    public function whereNull(string $columnName): self
    {
        $field = FieldFactory::createFieldWithRawValue(
            $columnName,
            'IS',
            'NULL',
        );

        $this->where->and($field);

        return $this;
    }

    public function whereNotNull(string $columnName): self
    {
        $field = FieldFactory::createFieldWithRawValue(
            $columnName,
            'IS NOT',
            'NULL',
        );

        $this->where->and($field);

        return $this;
    }

    public function orWhereNull(string $columnName): self
    {
        $field = FieldFactory::createFieldWithRawValue(
            $columnName,
            'IS',
            'NULL',
        );

        $this->where->or($field);

        return $this;
    }

    public function orWhereNotNull(string $columnName): self
    {
        $field = FieldFactory::createFieldWithRawValue(
            $columnName,
            'IS NOT',
            'NULL',
        );

        $this->where->or($field);

        return $this;
    }

    private function parseValues(array|Query $values): array
    {
        if ($values instanceof Query) {
            $this->addWhereValues($values->getValues());
            return [ValueFactory::createRawValue($values)];
        }

        return $values;
    }

    private function addWhereValues(array $values): void
    {
        foreach ($values as $value) {
            $this->where->addValue($value);
        }
    }
}
