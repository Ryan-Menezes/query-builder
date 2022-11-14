<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};
use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Values\RawValue;

class Field implements SqlInterface
{
    private Column $column;
    private string $operator;
    private ValueInterface|Column $value;

    public function __construct(string|Column $column, string $operator, mixed $value)
    {
        $this->column = $this->formatColumn($column);
        $this->operator = $operator;
        $this->value = $this->formatValue($value);
    }

    private function formatColumn(string|Column $column): Column
    {
        if(is_string($column)) {
            return new Column($column);
        }

        return $column;
    }

    private function formatValue(mixed $value): ValueInterface|Column
    {
        if($this->isColumn($value)) {
            return $this->formatColumn($value);
        }

        return ValueFactory::createValue($value);
    }

    private function isColumn(mixed $value): bool
    {
        return $value instanceof Column;
    }

    public function __toString(): string
    {
        return "`{$this->getColumnName()}` {$this->getOperator()} {$this->getFormattedValue()}";
    }

    private function getFormattedValue(): string|ValueInterface
    {
        if($this->isRawValue($this->value)) {
            return $this->value;
        }

        if($this->isColumn($this->value)) {
            return "`{$this->value->getName()}`";
        }

        return '?';
    }

    private function isRawValue(mixed $value): bool {
        return $value instanceof RawValue;
    }

    public function getColumnName(): string
    {
        return $this->column->getName();
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
