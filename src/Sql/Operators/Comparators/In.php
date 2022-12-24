<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Comparators;

use InvalidArgumentException;
use QueryBuilder\Factories\{FieldFactory, ValueFactory};
use QueryBuilder\Interfaces\{FieldInterface, ValueInterface};

class In implements FieldInterface
{
    private const SQL_IN_OPERATOR = 'IN';
    private const SQL_NOT_IN_OPERATOR = 'NOT IN';

    private ValueInterface $column;
    private array $values;
    private bool $isNotOperator = false;

    public function __construct(string $column, array $values)
    {
        if ($this->isNotValidValues($values)) {
            throw new InvalidArgumentException(
                'The array of values ​​must not be empty',
            );
        }

        $this->column = ValueFactory::createRawValue($column);
        $this->values = $values;
    }

    private function isNotValidValues(array $values): bool
    {
        return empty($values);
    }

    public function not(): self
    {
        $this->isNotOperator = true;
        return $this;
    }

    public function __toString(): string
    {
        $field = $this->getField();

        return "${field}";
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