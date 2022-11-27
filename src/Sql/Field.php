<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\{
    FieldInterface,
    ValueInterface,
};
use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Values\{
    CollectionValue,
    RawValue,
};

class Field implements FieldInterface
{
    private Column $column;
    private string $operator;
    private ValueInterface $value;

    public function __construct(Column $column, string $operator, ValueInterface $value)
    {
        $this->column =$column;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return "{$this->getColumn()} {$this->getOperator()} {$this->getFormattedValue()}";
    }

    private function getFormattedValue(): string|ValueInterface
    {
        if($this->isRawValue($this->value)) {
            return $this->value;
        }

        if($this->isCollectionValue($this->value)) {
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

    public function getColumn(): Column
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
