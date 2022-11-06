<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};

class Field implements SqlInterface
{
    public function __construct(
        private Column $column,
        private string $operator,
        private ValueInterface $value,
    )
    {}

    public function __toString(): string
    {
        return "{$this->column} {$this->operator} ?";
    }

    public function getColumnName(): string
    {
        return $this->column->getColumnName();
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
