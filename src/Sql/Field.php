<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{FieldInterface, ValueInterface};
use QueryBuilder\Sql\Values\{CollectionValue, RawValue};
use QueryBuilder\Exceptions\{
    InvalidArgumentColumnNameException,
    InvalidArgumentOperatorException,
};

class Field implements FieldInterface
{
    private ValueInterface $column;
    private string $operator;
    private ValueInterface $value;

    public function __construct(
        string $columnName,
        string $operator,
        ValueInterface $value,
    ) {
        if (empty($columnName)) {
            throw new InvalidArgumentColumnNameException(
                'The column name must be a string of length greater than zero.',
            );
        }

        if (empty($operator)) {
            throw new InvalidArgumentOperatorException(
                'The operator must be a string of length greater than zero.',
            );
        }

        $this->column = ValueFactory::createRawValue($columnName);
        $this->operator = $operator;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return "{$this->getColumn()} {$this->getOperator()} {$this->getFormattedValue()}";
    }

    private function getFormattedValue(): string|ValueInterface
    {
        if ($this->isRawValue($this->value)) {
            return $this->value;
        }

        if ($this->isCollectionValue($this->value)) {
            return $this->value;
        }

        return '?';
    }

    private function isRawValue(mixed $value): bool
    {
        return $value instanceof RawValue;
    }

    private function isCollectionValue(mixed $value): bool
    {
        return $value instanceof CollectionValue;
    }

    public function getColumn(): ValueInterface
    {
        return $this->column;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
