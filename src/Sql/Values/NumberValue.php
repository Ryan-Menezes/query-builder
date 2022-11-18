<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\ValueInterface;

class NumberValue implements ValueInterface
{
    private int|float $value;

    public function __construct(int|float $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string)$this->getValue();
    }

    public function getValue(): int|float
    {
        return $this->value;
    }
}
