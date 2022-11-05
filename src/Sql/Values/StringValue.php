<?php

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\SqlInterface;
use Stringable;

class StringValue implements SqlInterface
{
    private $value;

    public function __construct(string|Stringable $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return "'{$this->value}'";
    }
}
