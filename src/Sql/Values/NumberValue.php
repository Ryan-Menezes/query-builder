<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};

class NumberValue implements SqlInterface, ValueInterface
{
    private $value;

    public function __construct(int|float $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
