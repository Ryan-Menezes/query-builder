<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Comparators;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Factories\{FieldFactory, ValueFactory};
use QueryBuilder\Interfaces\{FieldInterface, ValueInterface};
use QueryBuilder\Sql\Values\{RawValue};
use QueryBuilder\Exceptions\{
    InvalidArgumentColumnNameException,
    InvalidArgumentValuesException,
};

class Between extends Sql implements FieldInterface
{
    private const SQL_BETWEEN_OPERATOR = 'BETWEEN';
    private const SQL_NOT_BETWEEN_OPERATOR = 'NOT BETWEEN';

    private ValueInterface $column;
    private ValueInterface $values;
    private bool $isNotOperator = false;

    public function __construct(string $columnName, array $values)
    {
        if ($this->isInvalidColumnName($columnName)) {
            throw new InvalidArgumentColumnNameException(
                'The column name must be a string of length greater than zero.',
            );
        }

        $this->column = ValueFactory::createRawValue($columnName);
        $this->values = $this->formatValues($values);
    }

    private function isInvalidColumnName(string $columnName): bool
    {
        return empty($columnName);
    }

    private function formatValues(array $values): ValueInterface
    {
        if ($this->isNotValidValues($values)) {
            throw new InvalidArgumentValuesException(
                'The array of values ​​must contain only two values.',
            );
        }

        foreach ($values as $key => $value) {
            $values[$key] = ValueFactory::createValue($value);
        }

        return ValueFactory::createCollectionValue($values);
    }

    private function isNotValidValues(array $values): bool
    {
        return count($values) !== 2;
    }

    public function not(): self
    {
        $this->isNotOperator = true;
        return $this;
    }

    public function toSql(): string
    {
        $field = $this->getField();
        return "{$field}";
    }

    private function getField(): FieldInterface
    {
        $column = (string) $this->column;
        $operator = $this->getOperator();
        $valuesToString = $this->getValuesToString();

        $field = FieldFactory::createFieldWithRawValue(
            $column,
            $operator,
            $valuesToString,
        );

        return $field;
    }

    private function getValuesToString(): string
    {
        $values = [];

        foreach ($this->values->getValue() as $v) {
            $values[] = $this->formatValueToString($v);
        }

        return implode(' AND ', $values);
    }

    private function formatValueToString(ValueInterface $value): string
    {
        if ($this->isRawValue($value)) {
            return (string) $value;
        }

        return '?';
    }

    private function isRawValue(mixed $value): bool
    {
        return $value instanceof RawValue;
    }

    public function getOperator(): string
    {
        if ($this->isNotOperator) {
            return self::SQL_NOT_BETWEEN_OPERATOR;
        }

        return self::SQL_BETWEEN_OPERATOR;
    }

    public function getColumn(): ValueInterface
    {
        $field = $this->getField();
        return $field->getColumn();
    }

    public function getValue(): ValueInterface
    {
        return $this->values;
    }
}
