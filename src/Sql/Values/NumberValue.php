<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Interfaces\ValueInterface;

class NumberValue extends Sql implements ValueInterface
{
    private int|float $value;

    public function __construct(int|float $value)
    {
        $this->value = $value;
    }

    public function toSql(): string
    {
        return (string) $this->getValue();
    }

    public function getValue(): int|float
    {
        return $this->value;
    }
}
