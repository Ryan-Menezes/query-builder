<?php

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\SqlInterface;

class NumberValue implements SqlInterface
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
