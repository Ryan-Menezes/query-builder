<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{
    FieldInterface,
    ValueInterface,
};
use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Values\RawValue;

class Field implements FieldInterface
{
    private Column $column;
    private string $operator;
    private ValueInterface|Column $value;

    public function __construct(string $column, string $operator, mixed $value)
    {
        $this->column = $this->formatColumn($column);
        $this->operator = $operator;
        $this->value = $this->formatValue($value);
    }

    private function formatValue(mixed $value): ValueInterface|Column
    {
        if($this->isColumn($value)) {
            return $this->formatColumn($value);
        }

        return ValueFactory::createValue($value);
    }

    private function formatColumn(string|Column $column): Column
    {
        if(is_string($column)) {
            return new Column($column);
        }

        return $column;
    }

    private function isColumn(mixed $value): bool
    {
        return $value instanceof Column;
    }

    public function __toString(): string
    {
        return "{$this->getColumn()} {$this->getOperator()} {$this->getFormattedValue()}";
    }

    private function getFormattedValue(): string|ValueInterface|Column
    {
        if($this->isRawValue($this->value)) {
            return $this->value;
        }

        if($this->isColumn($this->value)) {
            return $this->value;
        }

        return '?';
    }

    private function isRawValue(mixed $value): bool {
        return $value instanceof RawValue;
    }

    public function getColumn(): Column
    {
        return $this->column;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getValue(): mixed
    {
        if($this->isColumn($this->value)) {
            return $this->value->getName();
        }

        return $this->value->getValue();
    }
}
