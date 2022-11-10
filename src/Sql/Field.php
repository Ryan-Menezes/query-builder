<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};
use QueryBuilder\Sql\Values\RawValue;

class Field implements SqlInterface
{
    private Column $column;
    private string $operator;
    private ValueInterface $value;

    public function __construct(string|Column $column, string $operator, mixed $value)
    {
        $this->column = $this->formatColumn($column);
        $this->operator = $operator;
        $this->value = ValueFactory::createValue($value);
    }

    private function formatColumn(string|Column $column): Column
    {
        if(is_string($column)) {
            return new Column($column);
        }

        return $column;
    }

    public function __toString(): string
    {
        if($this->value instanceof RawValue) {
            return "`{$this->getColumnName()}` {$this->getOperator()} {$this->getValue()}";
        }

        return "`{$this->getColumnName()}` {$this->getOperator()} ?";
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
        return $this->value->getValue();
    }
}
