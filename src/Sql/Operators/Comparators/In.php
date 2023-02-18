<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Comparators;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Factories\{FieldFactory, ValueFactory};
use QueryBuilder\Interfaces\{FieldInterface, ValueInterface};
use QueryBuilder\Exceptions\{
    InvalidArgumentColumnNameException,
    InvalidArgumentValuesException,
};

class In extends Sql implements FieldInterface
{
    private const SQL_IN_OPERATOR = 'IN';
    private const SQL_NOT_IN_OPERATOR = 'NOT IN';

    private ValueInterface $column;
    private array $values;
    private bool $isNotOperator = false;

    public function __construct(string $columnName, array $values)
    {
        if ($this->isInvalidColumnName($columnName)) {
            throw new InvalidArgumentColumnNameException(
                'The column name must be a string of length greater than zero.',
            );
        }

        if ($this->isInvalidValues($values)) {
            throw new InvalidArgumentValuesException(
                'The array of values ​​must not be empty.',
            );
        }

        $this->column = ValueFactory::createRawValue($columnName);
        $this->values = $values;
    }

    private function isInvalidColumnName(string $columnName): bool
    {
        return empty($columnName);
    }

    private function isInvalidValues(array $values): bool
    {
        return empty($values);
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

        $field = FieldFactory::createField($column, $operator, $this->values);

        return $field;
    }

    public function getOperator(): string
    {
        if ($this->isNotOperator) {
            return self::SQL_NOT_IN_OPERATOR;
        }

        return self::SQL_IN_OPERATOR;
    }

    public function getColumn(): ValueInterface
    {
        $field = $this->getField();
        return $field->getColumn();
    }

    public function getValue(): ValueInterface
    {
        $field = $this->getField();
        return $field->getValue();
    }
}
