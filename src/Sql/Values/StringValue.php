<?php

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\SqlInterface;

class StringValue implements SqlInterface
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return "'{$this->value}'";
    }
}
