<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{FieldInterface, ValueInterface};
use QueryBuilder\Sql\Values\{CollectionValue, RawValue};

class Field implements FieldInterface
{
    private ValueInterface $column;
    private string $operator;
    private ValueInterface $value;

    public function __construct(
        string $column,
        string $operator,
        ValueInterface $value,
    ) {
        $this->column = ValueFactory::createRawValue($column);
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
