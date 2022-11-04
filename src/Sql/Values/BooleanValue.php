<?php

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\SqlInterface;

class BooleanValue implements SqlInterface
{
    private $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value ? '1' : '0';
    }
}
